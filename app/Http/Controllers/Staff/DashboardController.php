<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\ServiceProviderChangeRequest;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $myRequests = ServiceProviderChangeRequest::where('requested_by_user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $pendingCount = ServiceProviderChangeRequest::where('requested_by_user_id', Auth::id())
            ->where('status', 'pending')
            ->count();

        $approvedCount = ServiceProviderChangeRequest::where('requested_by_user_id', Auth::id())
            ->where('status', 'approved')
            ->count();

        $rejectedCount = ServiceProviderChangeRequest::where('requested_by_user_id', Auth::id())
            ->where('status', 'rejected')
            ->count();

        return view('staff.dashboard', compact('myRequests', 'pendingCount', 'approvedCount', 'rejectedCount'));
    }
}
