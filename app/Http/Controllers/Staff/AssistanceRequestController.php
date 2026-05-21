<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\AssistanceRequest;
use Illuminate\Http\Request;

class AssistanceRequestController extends Controller
{
    public function index()
    {
        $requests = AssistanceRequest::with('service')
            ->where('assigned_to', auth()->id())
            ->latest()
            ->get();

        return view('staff.assistance-requests.index', compact('requests'));
    }

    public function show(AssistanceRequest $assistanceRequest)
    {
        abort_unless($assistanceRequest->assigned_to === auth()->id(), 403);

        $assistanceRequest->load('service.subCategory.category');

        return view('staff.assistance-requests.show', compact('assistanceRequest'));
    }

    public function update(Request $request, AssistanceRequest $assistanceRequest)
    {
        abort_unless($assistanceRequest->assigned_to === auth()->id(), 403);

        $request->validate([
            'status'      => 'required|in:pending,contacted,completed',
            'staff_notes' => 'nullable|string|max:5000',
        ]);

        $assistanceRequest->update([
            'status'      => $request->status,
            'staff_notes' => $request->staff_notes,
        ]);

        return redirect()->back()->with('success', 'Request updated successfully.');
    }
}
