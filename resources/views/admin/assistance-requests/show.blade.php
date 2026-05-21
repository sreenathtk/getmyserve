@extends('admin.layouts.app')
@section('title', 'Assistance Request Detail')
@section('page-title', 'Assistance Request Detail')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="ri-checkbox-circle-line me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show">
            <i class="ri-information-line me-2"></i>{{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        {{-- Request Details --}}
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h5 class="card-title mb-1">{{ $assistanceRequest->full_name }}</h5>
                        <small class="text-muted">Submitted {{ $assistanceRequest->created_at->format('d M Y, h:i A') }}</small>
                    </div>
                    <form method="POST" action="{{ route('admin.assistance-requests.update-status', $assistanceRequest) }}"
                          class="d-flex gap-2 align-items-center">
                        @csrf @method('PATCH')
                        <select name="status" class="form-select form-select-sm" style="width:auto;">
                            <option value="pending"   {{ $assistanceRequest->status === 'pending'   ? 'selected' : '' }}>Pending</option>
                            <option value="contacted" {{ $assistanceRequest->status === 'contacted' ? 'selected' : '' }}>Contacted</option>
                            <option value="completed" {{ $assistanceRequest->status === 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                        <button type="submit" class="btn btn-sm btn-primary">Update</button>
                    </form>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <div class="text-muted small mb-1">Service</div>
                            <div class="fw-semibold">{{ $assistanceRequest->service->name ?? '—' }}</div>
                            @if($assistanceRequest->service?->subCategory)
                                <div class="text-muted small mt-1">
                                    {{ $assistanceRequest->service->subCategory->category->name ?? '' }}
                                    → {{ $assistanceRequest->service->subCategory->name }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <div class="text-muted small mb-1">Status</div>
                            @if($assistanceRequest->status === 'pending')
                                <span class="badge bg-warning-subtle text-warning-emphasis fs-6">Pending</span>
                            @elseif($assistanceRequest->status === 'contacted')
                                <span class="badge bg-info-subtle text-info-emphasis fs-6">Contacted</span>
                            @else
                                <span class="badge bg-success-subtle text-success-emphasis fs-6">Completed</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <div class="text-muted small mb-1">Email</div>
                            <div class="fw-semibold">{{ $assistanceRequest->email }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <div class="text-muted small mb-1">Phone</div>
                            <div class="fw-semibold">{{ $assistanceRequest->phone }}</div>
                        </div>
                    </div>
                    @if($assistanceRequest->whatsapp)
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <div class="text-muted small mb-1">WhatsApp</div>
                            <div class="fw-semibold">{{ $assistanceRequest->whatsapp }}</div>
                        </div>
                    </div>
                    @endif
                    @if($assistanceRequest->location)
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <div class="text-muted small mb-1">Location</div>
                            <div class="fw-semibold">{{ $assistanceRequest->location }}</div>
                        </div>
                    </div>
                    @endif
                    @if($assistanceRequest->language)
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <div class="text-muted small mb-1">Preferred Language</div>
                            <div class="fw-semibold">{{ $assistanceRequest->language }}</div>
                        </div>
                    </div>
                    @endif
                    @if($assistanceRequest->remarks)
                    <div class="col-12">
                        <div class="p-3 bg-light rounded">
                            <div class="text-muted small mb-1">Remarks</div>
                            <div>{{ $assistanceRequest->remarks }}</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Assign Staff --}}
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3 d-flex align-items-center gap-2">
                    <i class="ri-user-follow-line text-primary"></i>
                    Assign Staff
                </h5>

                @if($assistanceRequest->assignedStaff)
                <div class="d-flex align-items-center gap-3 p-3 rounded mb-3"
                     style="border:1px solid #e4e6ef;background:#f8f9fa;">
                    <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary text-white flex-shrink-0"
                         style="width:38px;height:38px;font-size:.85rem;font-weight:700;">
                        {{ strtoupper(substr($assistanceRequest->assignedStaff->name, 0, 1)) }}
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold">{{ $assistanceRequest->assignedStaff->name }}</div>
                        <div class="text-muted small">{{ $assistanceRequest->assignedStaff->email }}</div>
                    </div>
                    <form method="POST"
                          action="{{ route('admin.assistance-requests.assign-staff', $assistanceRequest) }}"
                          class="m-0">
                        @csrf @method('PATCH')
                        <input type="hidden" name="assigned_to" value="">
                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                onclick="return confirm('Remove the staff assignment?')">
                            <i class="ri-link-unlink me-1"></i>Unassign
                        </button>
                    </form>
                </div>
                @else
                <p class="text-muted small mb-3">This request has not been assigned to any staff member yet.</p>
                @endif

                <form method="POST"
                      action="{{ route('admin.assistance-requests.assign-staff', $assistanceRequest) }}"
                      class="d-flex gap-2 align-items-center">
                    @csrf @method('PATCH')
                    <select name="assigned_to" class="form-select form-select-sm" style="max-width:340px;" required>
                        <option value="">— Select staff member —</option>
                        @foreach($staffMembers as $staff)
                        <option value="{{ $staff->id }}"
                                {{ $assistanceRequest->assigned_to == $staff->id ? 'selected' : '' }}>
                            {{ $staff->name }} ({{ $staff->email }})
                        </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="ri-user-add-line me-1"></i>
                        {{ $assistanceRequest->assignedStaff ? 'Reassign' : 'Assign' }} Staff
                    </button>
                </form>
            </div>
        </div>

        {{-- Staff Notes (read-only for admin) --}}
        @if($assistanceRequest->staff_notes)
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3 d-flex align-items-center gap-2">
                    <i class="ri-sticky-note-line text-primary"></i>
                    Staff Notes
                </h5>
                <div class="p-3 bg-light rounded">
                    <p class="mb-0" style="white-space:pre-wrap;">{{ $assistanceRequest->staff_notes }}</p>
                </div>
                @if($assistanceRequest->assignedStaff)
                <div class="text-muted small mt-2">
                    <i class="ri-user-line me-1"></i>Notes by {{ $assistanceRequest->assignedStaff->name }}
                </div>
                @endif
            </div>
        </div>
        @endif

        <a href="{{ route('admin.assistance-requests.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="ri-arrow-left-line me-1"></i>Back to Assistance Requests
        </a>

    </div>
</div>
@endsection
