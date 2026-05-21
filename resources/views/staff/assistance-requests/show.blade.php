@extends('staff.layouts.app')
@section('title', 'Assistance Request')
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
                    <div>
                        @if($assistanceRequest->status === 'pending')
                            <span class="badge bg-warning-subtle text-warning-emphasis fs-6">Pending</span>
                        @elseif($assistanceRequest->status === 'contacted')
                            <span class="badge bg-info-subtle text-info-emphasis fs-6">Contacted</span>
                        @else
                            <span class="badge bg-success-subtle text-success-emphasis fs-6">Completed</span>
                        @endif
                    </div>
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
                            <div class="text-muted small mb-1">Email</div>
                            <div class="fw-semibold">{{ $assistanceRequest->email }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <div class="text-muted small mb-1">Phone</div>
                            <div class="fw-semibold">
                                <a href="tel:{{ $assistanceRequest->phone }}" class="text-dark text-decoration-none">
                                    {{ $assistanceRequest->phone }}
                                </a>
                            </div>
                        </div>
                    </div>
                    @if($assistanceRequest->whatsapp)
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <div class="text-muted small mb-1">WhatsApp</div>
                            <div class="fw-semibold">
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $assistanceRequest->whatsapp) }}"
                                   target="_blank" class="text-success text-decoration-none">
                                    <i class="ri-whatsapp-line me-1"></i>{{ $assistanceRequest->whatsapp }}
                                </a>
                            </div>
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

        {{-- Update Status & Notes --}}
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-4 d-flex align-items-center gap-2">
                    <i class="ri-edit-line text-primary"></i>
                    Update Request
                </h5>

                <form method="POST" action="{{ route('staff.assistance-requests.update', $assistanceRequest) }}">
                    @csrf @method('PATCH')

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status</label>
                        <div class="d-flex gap-2 flex-wrap">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status"
                                       id="status_pending" value="pending"
                                       {{ $assistanceRequest->status === 'pending' ? 'checked' : '' }}>
                                <label class="form-check-label" for="status_pending">
                                    <span class="badge bg-warning-subtle text-warning-emphasis">Pending</span>
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status"
                                       id="status_contacted" value="contacted"
                                       {{ $assistanceRequest->status === 'contacted' ? 'checked' : '' }}>
                                <label class="form-check-label" for="status_contacted">
                                    <span class="badge bg-info-subtle text-info-emphasis">Contacted Customer</span>
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status"
                                       id="status_completed" value="completed"
                                       {{ $assistanceRequest->status === 'completed' ? 'checked' : '' }}>
                                <label class="form-check-label" for="status_completed">
                                    <span class="badge bg-success-subtle text-success-emphasis">Completed</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="staff_notes" class="form-label fw-semibold">
                            Call Notes <span class="text-muted fw-normal">(optional)</span>
                        </label>
                        <textarea id="staff_notes"
                                  name="staff_notes"
                                  class="form-control"
                                  rows="4"
                                  placeholder="Record any notes from your interaction with the customer…">{{ $assistanceRequest->staff_notes }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="ri-save-line me-1"></i>Save Changes
                    </button>
                </form>
            </div>
        </div>

        <a href="{{ route('staff.assistance-requests.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="ri-arrow-left-line me-1"></i>Back to Assistance Requests
        </a>

    </div>
</div>
@endsection
