@extends('admin.layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-1">
                        <p class="text-muted mb-2 text-truncate">Total Service Providers</p>
                        <h4 class="mb-0">{{ $totalProviders }}</h4>
                    </div>
                    <div class="mini-stat-icon">
                        <div class="mini-stat-icon bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                            <i class="ri-building-2-line" style="font-size:22px;"></i>
                        </div>
                    </div>
                </div>
                <div class="border-top mt-3 pt-2">
                    <span class="badge badge-soft-success">{{ $activeProviders }} Active</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-1">
                        <p class="text-muted mb-2 text-truncate">Total Services</p>
                        <h4 class="mb-0">{{ $totalServices }}</h4>
                    </div>
                    <div>
                        <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                            <i class="ri-stack-line" style="font-size:22px;"></i>
                        </div>
                    </div>
                </div>
                <div class="border-top mt-3 pt-2">
                    <span class="badge badge-soft-success">{{ $activeServices }} Active</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-1">
                        <p class="text-muted mb-2 text-truncate">Pending Approvals</p>
                        <h4 class="mb-0">{{ $pendingApprovals }}</h4>
                    </div>
                    <div>
                        <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                            <i class="ri-time-line" style="font-size:22px;"></i>
                        </div>
                    </div>
                </div>
                <div class="border-top mt-3 pt-2">
                    <span class="badge badge-soft-info">{{ $totalCustomers }} Customers</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-1">
                        <p class="text-muted mb-2 text-truncate">Pending Change Requests</p>
                        <h4 class="mb-0">{{ $pendingChangeRequests }}</h4>
                    </div>
                    <div>
                        <div class="bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                            <i class="ri-file-list-3-line" style="font-size:22px;"></i>
                        </div>
                    </div>
                </div>
                <div class="border-top mt-3 pt-2">
                    <a href="{{ route('admin.change-requests.index') }}" class="badge badge-soft-primary text-decoration-none">View All Requests</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Quick Actions</h4>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <a href="{{ route('admin.service-providers.create') }}" class="btn btn-primary w-100 py-3">
                            <i class="ri-add-line me-2"></i> Add Service Provider
                        </a>
                    </div>
                    <div class="col-md-4 mb-3">
                        <a href="{{ route('admin.services.create') }}" class="btn btn-success w-100 py-3">
                            <i class="ri-add-line me-2"></i> Add New Service
                        </a>
                    </div>
                    <div class="col-md-4 mb-3">
                        <a href="{{ route('admin.service-providers.index') }}" class="btn btn-info w-100 py-3 text-white">
                            <i class="ri-list-check me-2"></i> View All Providers
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
