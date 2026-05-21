<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use App\Models\EnquiryFile;
use App\Models\Order;
use App\Models\OrderFile;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $serviceIds = $this->accessibleServiceIds();

        $linkedUserIds = Enquiry::whereIn('service_id', $serviceIds)
            ->whereNotNull('user_id')
            ->pluck('user_id')
            ->unique()
            ->toArray();

        $linkedEmails = Enquiry::whereIn('service_id', $serviceIds)
            ->whereNull('user_id')
            ->pluck('email')
            ->filter()
            ->unique()
            ->toArray();

        $query = User::where('role', User::ROLE_CUSTOMER)
            ->where(function ($q) use ($linkedUserIds, $linkedEmails) {
                $q->whereIn('id', $linkedUserIds)
                  ->orWhereIn('email', $linkedEmails);
            })
            ->orderBy('name');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($qb) use ($s) {
                $qb->where('name', 'like', "%{$s}%")
                   ->orWhere('email', 'like', "%{$s}%")
                   ->orWhere('phone', 'like', "%{$s}%")
                   ->orWhere('city', 'like', "%{$s}%");
            });
        }

        if ($request->filled('country')) {
            $query->where('country', $request->country);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $customers = $query->withCount('reviews')->get();
        $countries = RegisterController::GCC_COUNTRIES;

        return view('staff.customers.index', compact('customers', 'countries'));
    }

    public function show(User $customer)
    {
        abort_unless($customer->isCustomer(), 404);

        $serviceIds = $this->accessibleServiceIds();

        $hasAccess = Enquiry::whereIn('service_id', $serviceIds)
            ->where(function ($q) use ($customer) {
                $q->where('user_id', $customer->id)
                  ->orWhere(function ($inner) use ($customer) {
                      $inner->whereNull('user_id')->where('email', $customer->email);
                  });
            })
            ->exists();

        abort_unless($hasAccess, 403);

        $enquiries = Enquiry::with(['service', 'files'])
            ->whereIn('service_id', $serviceIds)
            ->where(function ($q) use ($customer) {
                $q->where('user_id', $customer->id)
                  ->orWhere(function ($inner) use ($customer) {
                      $inner->whereNull('user_id')->where('email', $customer->email);
                  });
            })
            ->latest()
            ->get();

        $orders = Order::with(['orderItems', 'files'])
            ->where('user_id', $customer->id)
            ->latest()
            ->get();

        return view('staff.customers.show', compact('customer', 'enquiries', 'orders'));
    }

    public function linkFile(Request $request, User $customer, EnquiryFile $file)
    {
        abort_unless($customer->isCustomer(), 404);

        $request->validate([
            'target_type' => 'required|in:enquiry,order',
            'target_id'   => 'required|integer',
        ]);

        if ($request->target_type === 'order') {
            $order = Order::where('id', $request->target_id)
                ->where('user_id', $customer->id)
                ->firstOrFail();

            if (OrderFile::where('order_id', $order->id)->where('file_path', $file->file_path)->exists()) {
                return redirect()->back()->with('info', 'This file is already linked to that order.');
            }

            OrderFile::create([
                'order_id'      => $order->id,
                'file_name'     => $file->file_name,
                'original_name' => $file->original_name,
                'file_path'     => $file->file_path,
                'file_size'     => $file->file_size,
                'mime_type'     => null,
                'uploaded_by'   => auth()->id(),
            ]);

            return redirect()->back()->with('success', 'File linked to Order #' . str_pad($order->id, 6, '0', STR_PAD_LEFT) . ' successfully.');
        }

        $serviceIds    = $this->accessibleServiceIds();
        $targetEnquiry = Enquiry::whereIn('service_id', $serviceIds)
            ->where('id', $request->target_id)
            ->where(function ($q) use ($customer) {
                $q->where('user_id', $customer->id)
                  ->orWhere(fn($i) => $i->whereNull('user_id')->where('email', $customer->email));
            })
            ->firstOrFail();

        if (EnquiryFile::where('enquiry_id', $targetEnquiry->id)->where('file_path', $file->file_path)->exists()) {
            return redirect()->back()->with('info', 'This file is already linked to that service request.');
        }

        EnquiryFile::create([
            'enquiry_id'    => $targetEnquiry->id,
            'file_name'     => $file->file_name,
            'original_name' => $file->original_name,
            'file_path'     => $file->file_path,
            'file_size'     => $file->file_size,
        ]);

        return redirect()->back()->with('success', 'File linked to "' . ($targetEnquiry->service->name ?? 'service request') . '" successfully.');
    }

    public function linkOrderFile(Request $request, User $customer, OrderFile $file)
    {
        abort_unless($customer->isCustomer(), 404);

        $request->validate([
            'target_type' => 'required|in:enquiry,order',
            'target_id'   => 'required|integer',
        ]);

        if ($request->target_type === 'order') {
            $order = Order::where('id', $request->target_id)
                ->where('user_id', $customer->id)
                ->firstOrFail();

            if (OrderFile::where('order_id', $order->id)->where('file_path', $file->file_path)->exists()) {
                return redirect()->back()->with('info', 'This file is already linked to that order.');
            }

            OrderFile::create([
                'order_id'      => $order->id,
                'file_name'     => $file->file_name,
                'original_name' => $file->original_name,
                'file_path'     => $file->file_path,
                'file_size'     => $file->file_size,
                'mime_type'     => $file->mime_type,
                'uploaded_by'   => auth()->id(),
            ]);

            return redirect()->back()->with('success', 'File linked to Order #' . str_pad($order->id, 6, '0', STR_PAD_LEFT) . ' successfully.');
        }

        $serviceIds    = $this->accessibleServiceIds();
        $targetEnquiry = Enquiry::whereIn('service_id', $serviceIds)
            ->where('id', $request->target_id)
            ->where(function ($q) use ($customer) {
                $q->where('user_id', $customer->id)
                  ->orWhere(fn($i) => $i->whereNull('user_id')->where('email', $customer->email));
            })
            ->firstOrFail();

        if (EnquiryFile::where('enquiry_id', $targetEnquiry->id)->where('file_path', $file->file_path)->exists()) {
            return redirect()->back()->with('info', 'This file is already linked to that service request.');
        }

        EnquiryFile::create([
            'enquiry_id'    => $targetEnquiry->id,
            'file_name'     => $file->file_name,
            'original_name' => $file->original_name,
            'file_path'     => $file->file_path,
            'file_size'     => $file->file_size,
        ]);

        return redirect()->back()->with('success', 'File linked to "' . ($targetEnquiry->service->name ?? 'service request') . '" successfully.');
    }

    private function accessibleServiceIds(): array
    {
        $assignments = auth()->user()->staffAssignments;

        $explicit = $assignments->whereNotNull('service_id')->pluck('service_id')->toArray();

        $categoryIds = $assignments->whereNull('service_id')->pluck('category_id')->toArray();

        $fromCategories = [];
        if (!empty($categoryIds)) {
            $fromCategories = Service::whereHas('subCategory', function ($q) use ($categoryIds) {
                $q->whereIn('category_id', $categoryIds);
            })->pluck('id')->toArray();
        }

        return array_values(array_unique(array_merge($explicit, $fromCategories)));
    }
}
