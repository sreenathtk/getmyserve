@extends('provider.layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Provider Dashboard')

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card text-center py-4">
            <div class="card-body">
                <i class="ri-mail-send-line text-primary" style="font-size:2.5rem;"></i>
                <h4 class="mt-3 fw-bold">
                    {{ \App\Models\Enquiry::where('assigned_sp_id', auth()->id())->count() }}
                </h4>
                <p class="text-muted mb-3">Assigned Service Requests</p>
                <a href="{{ route('provider.enquiries.index') }}" class="btn btn-primary btn-sm">
                    <i class="ri-eye-line me-1"></i>View All
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center py-4">
            <div class="card-body">
                <i class="ri-loader-4-line text-info" style="font-size:2.5rem;"></i>
                <h4 class="mt-3 fw-bold">
                    {{ \App\Models\Enquiry::where('assigned_sp_id', auth()->id())->where('status', 'under_processing')->count() }}
                </h4>
                <p class="text-muted mb-3">Under Processing</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center py-4">
            <div class="card-body">
                <i class="ri-checkbox-circle-line text-success" style="font-size:2.5rem;"></i>
                <h4 class="mt-3 fw-bold">
                    {{ \App\Models\Enquiry::where('assigned_sp_id', auth()->id())->where('status', 'resolved')->count() }}
                </h4>
                <p class="text-muted mb-3">Resolved</p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body text-center py-4">
        <h5>Welcome, {{ Auth::user()->name }}!</h5>
        <p class="text-muted">Use the sidebar to navigate your service requests and update your profile.</p>
        <a href="{{ route('provider.change-requests.create') }}" class="btn btn-outline-primary">
            <i class="ri-edit-line me-1"></i>Request Profile Update
        </a>
    </div>
</div>
@endsection
