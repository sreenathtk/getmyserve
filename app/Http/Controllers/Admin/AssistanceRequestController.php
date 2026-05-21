<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssistanceRequest;
use App\Models\User;
use Illuminate\Http\Request;

class AssistanceRequestController extends Controller
{
    public function index()
    {
        $requests = AssistanceRequest::with(['service', 'assignedStaff'])->latest()->get();

        return view('admin.assistance-requests.index', compact('requests'));
    }

    public function show(AssistanceRequest $assistanceRequest)
    {
        $assistanceRequest->load('service.subCategory.category', 'customer', 'assignedStaff');

        $staffMembers = User::where('role', User::ROLE_STAFF)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return view('admin.assistance-requests.show', compact('assistanceRequest', 'staffMembers'));
    }

    public function assignStaff(Request $request, AssistanceRequest $assistanceRequest)
    {
        $request->validate([
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        if ($request->filled('assigned_to')) {
            $staff = User::where('id', $request->assigned_to)
                ->where('role', User::ROLE_STAFF)
                ->firstOrFail();
            $assistanceRequest->update(['assigned_to' => $staff->id]);
            return redirect()->back()->with('success', "Request assigned to {$staff->name}.");
        }

        $assistanceRequest->update(['assigned_to' => null]);
        return redirect()->back()->with('success', 'Staff assignment removed.');
    }

    public function updateStatus(Request $request, AssistanceRequest $assistanceRequest)
    {
        $request->validate(['status' => 'required|in:pending,contacted,completed']);

        if ($assistanceRequest->status === $request->status) {
            return redirect()->back()->with('info', 'Status is already set to that value.');
        }

        $assistanceRequest->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Status updated.');
    }
}
