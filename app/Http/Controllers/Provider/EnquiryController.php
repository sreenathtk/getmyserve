<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use App\Models\EnquiryStatusLog;
use App\Models\EnquiryUpdate;
use Illuminate\Http\Request;

class EnquiryController extends Controller
{
    public function index()
    {
        $enquiries = Enquiry::with('service')
            ->where('assigned_sp_id', auth()->id())
            ->latest()
            ->get();

        return view('provider.enquiries.index', compact('enquiries'));
    }

    public function show(Enquiry $enquiry)
    {
        abort_unless($enquiry->assigned_sp_id === auth()->id(), 403);

        $enquiry->load(
            'service.subCategory.category',
            'files',
            'updates.author'
        );

        return view('provider.enquiries.show', compact('enquiry'));
    }

    public function storeUpdate(Request $request, Enquiry $enquiry)
    {
        abort_unless($enquiry->assigned_sp_id === auth()->id(), 403);

        $data = $request->validate([
            'note'   => 'nullable|string|max:2000',
            'status' => 'nullable|in:under_processing,completed',
        ]);

        if (empty($data['note']) && empty($data['status'])) {
            return redirect()->back()->withErrors(['note' => 'Please add a note or select a status.']);
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
        }

        EnquiryUpdate::create([
            'enquiry_id' => $enquiry->id,
            'user_id'    => auth()->id(),
            'note'       => $data['note'] ?? null,
            'status'     => $statusChanged,
        ]);

        return redirect()->back()->with('success', 'Update posted successfully.');
    }
}
