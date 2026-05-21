<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use App\Models\EnquiryFile;
use App\Models\Order;
use App\Models\OrderFile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', User::ROLE_CUSTOMER)->orderBy('name');

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($qb) use ($q) {
                $qb->where('name', 'like', "%{$q}%")
                   ->orWhere('email', 'like', "%{$q}%")
                   ->orWhere('phone', 'like', "%{$q}%")
                   ->orWhere('city', 'like', "%{$q}%");
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

        return view('admin.customers.index', compact('customers', 'countries'));
    }

    public function create()
    {
        return view('admin.customers.create', [
            'countries' => RegisterController::GCC_COUNTRIES,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone'    => ['required', 'string', 'max:20'],
            'country'  => ['required', 'in:' . implode(',', array_keys(RegisterController::GCC_COUNTRIES))],
            'city'     => ['required', 'string', 'max:100'],
            'address'  => ['required', 'string', 'max:500'],
            'password' => ['required', Password::min(8)],
        ]);

        User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'phone'     => $request->phone,
            'country'   => $request->country,
            'city'      => $request->city,
            'address'   => $request->address,
            'password'  => Hash::make($request->password),
            'role'      => User::ROLE_CUSTOMER,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer created successfully.');
    }

    public function show(User $customer)
    {
        abort_unless($customer->isCustomer(), 404);

        $enquiries = Enquiry::with(['service', 'files'])
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

        return view('admin.customers.show', compact('customer', 'enquiries', 'orders'));
    }

    public function edit(User $customer)
    {
        abort_unless($customer->isCustomer(), 404);

        return view('admin.customers.edit', [
            'customer'  => $customer,
            'countries' => RegisterController::GCC_COUNTRIES,
        ]);
    }

    public function update(Request $request, User $customer)
    {
        abort_unless($customer->isCustomer(), 404);

        $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['required', 'email', 'max:255', 'unique:users,email,' . $customer->id],
            'phone'   => ['required', 'string', 'max:20'],
            'country' => ['required', 'in:' . implode(',', array_keys(RegisterController::GCC_COUNTRIES))],
            'city'    => ['required', 'string', 'max:100'],
            'address' => ['required', 'string', 'max:500'],
            'password' => $request->filled('password') ? ['required', Password::min(8)] : [],
        ]);

        $data = [
            'name'      => $request->name,
            'email'     => $request->email,
            'phone'     => $request->phone,
            'country'   => $request->country,
            'city'      => $request->city,
            'address'   => $request->address,
            'is_active' => $request->boolean('is_active'),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $customer->update($data);

        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer updated successfully.');
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

        $targetEnquiry = Enquiry::where('id', $request->target_id)
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

        $targetEnquiry = Enquiry::where('id', $request->target_id)
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

    public function toggleStatus(User $customer)
    {
        abort_unless($customer->isCustomer(), 404);

        $customer->update(['is_active' => !$customer->is_active]);

        $status = $customer->is_active ? 'enabled' : 'disabled';
        return redirect()->route('admin.customers.index')
            ->with('success', "Customer {$status} successfully.");
    }
}
