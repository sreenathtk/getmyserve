<?php

namespace App\Http\Controllers\Staff;

use App\Events\Customer\EnquiryCompleted;
use App\Events\Customer\EnquiryStatusUpdated;
use App\Events\Provider\ProviderStatusUpdateRequested;
use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use App\Models\EnquiryStatusLog;
use App\Models\EnquiryUpdate;
use App\Models\EnquiryViewLog;
use App\Models\Service;
use Illuminate\Http\Request;

class EnquiryController extends Controller
{
    public function index()
    {
        $serviceIds = $this->accessibleServiceIds();

        $enquiries = Enquiry::with('service')
            ->whereIn('service_id', $serviceIds)
            ->latest()
            ->get();

        return view('staff.enquiries.index', compact('enquiries'));
    }

    public function show(Enquiry $enquiry)
    {
        abort_unless(in_array($enquiry->service_id, $this->accessibleServiceIds()), 403);

        EnquiryViewLog::firstOrCreate([
            'enquiry_id' => $enquiry->id,
            'user_id'    => auth()->id(),
        ]);

        $enquiry->load(
            'service.subCategory.category',
            'files',
            'viewLogs.viewer',
            'statusLogs.changedBy',
            'updates.author',
            'assignedSp.serviceProvider'
        );

        return view('staff.enquiries.show', compact('enquiry'));
    }

    public function storeUpdate(Request $request, Enquiry $enquiry)
    {
        abort_unless(in_array($enquiry->service_id, $this->accessibleServiceIds()), 403);

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
        abort_unless(in_array($enquiry->service_id, $this->accessibleServiceIds()), 403);

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

        return redirect()->back()->with('success', 'Status updated successfully.');
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
