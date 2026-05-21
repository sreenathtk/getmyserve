<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceProvider;
use App\Models\ServiceProviderChangeRequest;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProviders = ServiceProvider::count();
        $activeProviders = ServiceProvider::where('is_active', true)->count();
        $totalServices = Service::count();
        $activeServices = Service::where('is_active', true)->count();
        $pendingApprovals = ServiceProvider::where('approval_status', 'pending')->count();
        $totalCustomers = User::where('role', 'customer')->count();
        $pendingChangeRequests = ServiceProviderChangeRequest::where('status', 'pending')->count();

        return view('admin.dashboard', compact(
            'totalProviders', 'activeProviders', 'totalServices',
            'activeServices', 'pendingApprovals', 'totalCustomers',
            'pendingChangeRequests'
        ));
    }
}
