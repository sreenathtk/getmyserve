<?php

namespace App\Http\Controllers\Admin;

use App\Events\Customer\EnquiryCompleted;
use App\Events\Customer\EnquiryStatusUpdated;
use App\Events\Provider\ProviderEnquiryAssigned;
use App\Events\Provider\ProviderStatusUpdateRequested;
use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use App\Models\EnquiryFile;
use App\Models\EnquiryStatusLog;
use App\Models\EnquiryUpdate;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Stripe\Refund;
use Stripe\Stripe;

class EnquiryController extends Controller
{
    public function index()
    {
        $enquiries = Enquiry::with('service')->latest()->get();

        return view('admin.enquiries.index', compact('enquiries'));
    }

    public function show(Enquiry $enquiry)
    {
        $enquiry->load(
            'service.subCategory.category',
            'files',
            'customer',
            'assignedSp',
            'viewLogs.viewer',
            'statusLogs.changedBy',
            'updates.author'
        );

        $customers = User::where('role', User::ROLE_CUSTOMER)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $serviceProviders = User::where('role', User::ROLE_SERVICE_PROVIDER)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return view('admin.enquiries.show', compact('enquiry', 'customers', 'serviceProviders'));
    }

    public function linkCustomer(Request $request, Enquiry $enquiry)
    {
        $request->validate([
            'customer_id' => 'nullable|exists:users,id',
        ]);

        if ($request->filled('customer_id')) {
            $customer = User::where('id', $request->customer_id)
                ->where('role', User::ROLE_CUSTOMER)
                ->firstOrFail();
            $enquiry->update(['user_id' => $customer->id]);
            return redirect()->back()->with('success', "Enquiry linked to {$customer->name}.");
        }

        $enquiry->update(['user_id' => null]);
        return redirect()->back()->with('success', 'Customer link removed.');
    }

    public function assignSp(Request $request, Enquiry $enquiry)
    {
        $request->validate([
            'sp_id' => 'nullable|exists:users,id',
        ]);

        $enquiry->update(['assigned_sp_id' => $request->filled('sp_id') ? $request->sp_id : null]);

        if ($request->filled('sp_id')) {
            event(new ProviderEnquiryAssigned($enquiry));
        }

        $msg = $request->filled('sp_id')
            ? 'Service provider assigned successfully.'
            : 'Service provider assignment removed.';

        return redirect()->back()->with('success', $msg);
    }

    public function storeUpdate(Request $request, Enquiry $enquiry)
    {
        $data = $request->validate([
            'note'      => 'nullable|string|max:2000',
            'status'    => 'nullable|in:pending,in_progress,under_processing,completed,resolved',
            'notify_sp' => 'nullable|boolean',
        ]);

        if (empty($data['note']) && empty($data['status'])) {
            return redirect()->back()->withErrors(['note' => 'Please add a note or select a new status.']);
        }

        $statusChanged = null;
        if (!empty($data['status']) && $data['status'] !== $enquiry->status) {
            $oldStatus = $enquiry->status;
            $enquiry->update(['status' => $data['status']]);

            EnquiryStatusLog::create([
                'enquiry_id' => $enquiry->id,
                'changed_by' => auth()->id(),
                'old_status' => $oldStatus,
                'new_status' => $data['status'],
            ]);

            $statusChanged = $data['status'];
            event(new EnquiryStatusUpdated($enquiry, $data['status']));

            if (in_array($data['status'], ['completed', 'resolved'])) {
                event(new EnquiryCompleted($enquiry));
            }
        }

        EnquiryUpdate::create([
            'enquiry_id' => $enquiry->id,
            'user_id'    => auth()->id(),
            'note'       => $data['note'] ?? null,
            'status'     => $statusChanged,
        ]);

        if (!empty($data['notify_sp']) && $enquiry->assigned_sp_id && !empty($data['note'])) {
            event(new ProviderStatusUpdateRequested($enquiry, $data['note']));
        }

        return redirect()->back()->with('success', 'Update posted.');
    }

    public function updateStatus(Request $request, Enquiry $enquiry)
    {
        $request->validate(['status' => 'required|in:pending,in_progress,under_processing,completed,resolved']);

        $oldStatus = $enquiry->status;

        if ($oldStatus === $request->status) {
            return redirect()->back()->with('info', 'Status is already set to that value.');
        }

        $enquiry->update(['status' => $request->status]);

        EnquiryStatusLog::create([
            'enquiry_id' => $enquiry->id,
            'changed_by' => auth()->id(),
            'old_status' => $oldStatus,
            'new_status' => $request->status,
        ]);

        event(new EnquiryStatusUpdated($enquiry, $request->status));

        if (in_array($request->status, ['completed', 'resolved'])) {
            event(new EnquiryCompleted($enquiry));
        }

        return redirect()->back()->with('success', 'Status updated.');
    }

    public function updatePrice(Request $request, Enquiry $enquiry)
    {
        $request->validate([
            'quoted_price' => 'required|numeric|min:0.01',
        ]);

        $enquiry->update([
            'quoted_price' => $request->quoted_price,
            'quoted_by'    => auth()->id(),
            'quoted_at'    => now(),
        ]);

        return redirect()->back()->with('success', 'Price updated successfully.');
    }

    public function refund(Request $request, Enquiry $enquiry)
    {
        if (!$enquiry->isRefundable()) {
            return redirect()->back()->with('error', 'This request is not eligible for a refund.');
        }

        $request->validate([
            'refund_amount' => 'required|numeric|min:0.01|max:' . $enquiry->getRefundableAmount(),
        ]);

        Stripe::setApiKey(config('services.stripe.secret'));

        Refund::create([
            'payment_intent' => $enquiry->stripe_payment_intent_id,
            'amount'         => (int) round($request->refund_amount * 100),
        ]);

        $newRefunded  = (float) $enquiry->refunded_amount + (float) $request->refund_amount;
        $fullyRefunded = $newRefunded >= (float) $enquiry->paid_amount;

        $enquiry->update([
            'refunded_amount' => $newRefunded,
            'payment_status'  => $fullyRefunded ? 'refunded' : 'paid',
        ]);

        Payment::create([
            'enquiry_id'               => $enquiry->id,
            'stripe_payment_intent_id' => $enquiry->stripe_payment_intent_id,
            'type'                     => 'refund',
            'amount'                   => $request->refund_amount,
            'currency'                 => 'AED',
            'status'                   => 'succeeded',
        ]);

        return redirect()->back()->with('success', 'Refund of AED ' . number_format($request->refund_amount, 2) . ' processed successfully.');
    }

    public function storeFiles(Request $request, Enquiry $enquiry)
    {
        $request->validate([
            'files'        => 'required|array|min:1',
            'files.*.name' => 'required|string|max:255',
            'files.*.file' => 'required|file|max:20480',
        ]);

        $uploadedFiles = $request->file('files', []);
        $fileNames     = $request->input('files', []);
        $count = 0;

        foreach ($uploadedFiles as $idx => $group) {
            $uploadedFile = $group['file'] ?? null;
            $displayName  = $fileNames[$idx]['name'] ?? null;

            if (! $uploadedFile || ! $displayName) {
                continue;
            }

            $path = $uploadedFile->store("enquiry-files/{$enquiry->id}", 'public');

            EnquiryFile::create([
                'enquiry_id'    => $enquiry->id,
                'file_name'     => $displayName,
                'original_name' => $uploadedFile->getClientOriginalName(),
                'file_path'     => $path,
                'file_size'     => $uploadedFile->getSize(),
            ]);

            $count++;
        }

        return redirect()->back()->with('success', "{$count} file(s) uploaded successfully.");
    }

    public function destroyFile(Enquiry $enquiry, EnquiryFile $file)
    {
        abort_unless($file->enquiry_id === $enquiry->id, 404);

        Storage::disk('public')->delete($file->file_path);
        $file->delete();

        return redirect()->back()->with('success', 'File deleted.');
    }
}
