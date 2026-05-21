@extends('staff.layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Staff Dashboard')

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-1">
                        <p class="text-muted mb-2">Pending Requests</p>
                        <h4 class="mb-0">{{ $pendingCount }}</h4>
                    </div>
                    <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                        <i class="ri-time-line" style="font-size:22px;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-1">
                        <p class="text-muted mb-2">Approved Requests</p>
                        <h4 class="mb-0">{{ $approvedCount }}</h4>
                    </div>
                    <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                        <i class="ri-checkbox-circle-line" style="font-size:22px;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-1">
                        <p class="text-muted mb-2">Rejected Requests</p>
                        <h4 class="mb-0">{{ $rejectedCount }}</h4>
                    </div>
                    <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                        <i class="ri-close-circle-line" style="font-size:22px;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Quick Actions</h4>
                <div class="d-grid gap-2">
                    <a href="{{ route('staff.change-requests.create', ['type' => 'create']) }}" class="btn btn-primary py-3">
                        <i class="ri-add-circle-line me-2"></i> Request New Service Provider
                    </a>
                    <a href="{{ route('staff.change-requests.create', ['type' => 'update']) }}" class="btn btn-outline-primary py-3">
                        <i class="ri-edit-line me-2"></i> Request Provider Update
                    </a>
                    <a href="{{ route('staff.change-requests.index') }}" class="btn btn-outline-secondary py-3">
                        <i class="ri-list-check me-2"></i> View All My Requests
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Recent Requests</h4>
                @forelse($myRequests as $req)
                <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                    <div class="flex-1">
                        <div class="fw-semibold">
                            {{ $req->request_type === 'create' ? 'New Provider' : 'Update Provider' }}
                            @if($req->serviceProvider)
                                — {{ $req->serviceProvider->company_name }}
                            @else
                                — {{ $req->payload['provider']['company_name'] ?? '—' }}
                            @endif
                        </div>
                        <small class="text-muted">{{ $req->created_at->diffForHumans() }}</small>
                    </div>
                    @if($req->status === 'approved')
                        <span class="badge badge-soft-success">Approved</span>
                    @elseif($req->status === 'rejected')
                        <span class="badge badge-soft-danger">Rejected</span>
                    @else
                        <span class="badge badge-soft-warning">Pending</span>
                    @endif
                </div>
                @empty
                <p class="text-muted mb-0">No requests yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
