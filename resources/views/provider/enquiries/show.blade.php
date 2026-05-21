@extends('provider.layouts.app')
@section('title', 'Service Request')
@section('page-title', 'Service Request Detail')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">

        {{-- Enquiry Details --}}
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h5 class="card-title mb-1">{{ $enquiry->first_name }} {{ $enquiry->last_name }}</h5>
                        <small class="text-muted">Submitted {{ $enquiry->created_at->format('d M Y, h:i A') }}</small>
                    </div>
                    @php
                        $badge = match($enquiry->status) {
                            'pending'          => 'bg-warning-subtle text-warning-emphasis',
                            'in_progress'      => 'bg-info-subtle text-info-emphasis',
                            'under_processing' => 'bg-primary-subtle text-primary-emphasis',
                            'completed'        => 'badge-soft-teal',
                            'resolved'         => 'bg-success-subtle text-success-emphasis',
                            default            => 'bg-secondary-subtle text-secondary-emphasis',
                        };
                        $label = match($enquiry->status) {
                            'pending'          => 'Pending',
                            'in_progress'      => 'In Progress',
                            'under_processing' => 'Under Processing',
                            'completed'        => 'Completed',
                            'resolved'         => 'Resolved',
                            default            => ucfirst($enquiry->status),
                        };
                    @endphp
                    <span class="badge {{ $badge }} fs-6">{{ $label }}</span>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <div class="text-muted small mb-1">Service</div>
                            <div class="fw-semibold">{{ $enquiry->service->name ?? '—' }}</div>
                            @if($enquiry->service?->subCategory)
                                <div class="text-muted small mt-1">
                                    {{ $enquiry->service->subCategory->category->name ?? '' }}
                                    → {{ $enquiry->service->subCategory->name }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <div class="text-muted small mb-1">Email</div>
                            <div class="fw-semibold">{{ $enquiry->email }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <div class="text-muted small mb-1">Phone</div>
                            <div class="fw-semibold">
                                <a href="tel:{{ $enquiry->phone }}" class="text-decoration-none">{{ $enquiry->phone }}</a>
                            </div>
                        </div>
                    </div>
                    @if($enquiry->whatsapp)
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <div class="text-muted small mb-1">WhatsApp</div>
                            <div class="fw-semibold">
                                <a href="https://wa.me/{{ preg_replace('/\D/','',$enquiry->whatsapp) }}"
                                   target="_blank" class="text-decoration-none text-success">
                                    {{ $enquiry->whatsapp }}
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif
                    @if($enquiry->location)
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <div class="text-muted small mb-1">Location</div>
                            <div class="fw-semibold">{{ $enquiry->location }}</div>
                        </div>
                    </div>
                    @endif
                    @if($enquiry->language)
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <div class="text-muted small mb-1">Preferred Language</div>
                            <div class="fw-semibold">{{ $enquiry->language }}</div>
                        </div>
                    </div>
                    @endif
                    @if($enquiry->remarks)
                    <div class="col-12">
                        <div class="p-3 bg-light rounded">
                            <div class="text-muted small mb-1">Remarks</div>
                            <div>{{ $enquiry->remarks }}</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Related Files --}}
        @if($enquiry->files->isNotEmpty())
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-4 d-flex align-items-center gap-2">
                    <i class="ri-folder-open-line text-primary"></i>
                    Documents
                    <span class="badge bg-primary-subtle text-primary-emphasis ms-1">{{ $enquiry->files->count() }}</span>
                </h5>
                @foreach($enquiry->files as $file)
                <div class="d-flex align-items-center gap-3 p-3 mb-2 rounded"
                     style="border:1px solid #e4e6ef;background:#fafafa;">
                    <i class="{{ $file->icon_class }}" style="font-size:1.5rem;flex-shrink:0;"></i>
                    <div class="flex-grow-1 min-w-0">
                        <div class="fw-semibold text-truncate">{{ $file->file_name }}</div>
                        <div class="text-muted small">
                            {{ $file->original_name }} &nbsp;·&nbsp; {{ $file->formatted_size }}
                        </div>
                    </div>
                    <a href="{{ Storage::url($file->file_path) }}"
                       target="_blank"
                       class="btn btn-sm btn-outline-primary flex-shrink-0">
                        <i class="ri-download-line me-1"></i>Download
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Updates & Comments --}}
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-4 d-flex align-items-center gap-2">
                    <i class="ri-chat-3-line text-primary"></i>
                    Updates & Comments
                    @if($enquiry->updates->isNotEmpty())
                        <span class="badge bg-primary-subtle text-primary-emphasis ms-1">{{ $enquiry->updates->count() }}</span>
                    @endif
                </h5>

                {{-- Timeline --}}
                @if($enquiry->updates->isEmpty())
                    <p class="text-muted small fst-italic mb-4">No updates posted yet.</p>
                @else
                    <div class="mb-4" style="max-height:400px;overflow-y:auto;">
                        @foreach($enquiry->updates as $update)
                        <div class="d-flex gap-3 mb-3 p-3 rounded" style="background:#f8f9fa;">
                            <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0 text-white fw-bold"
                                 style="width:36px;height:36px;font-size:.8rem;background:{{ $update->author->role === 'service_provider' ? '#059669' : ($update->author->role === 'staff' ? '#2563eb' : '#5664d2') }};">
                                {{ strtoupper(substr($update->author->name ?? '?', 0, 1)) }}
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="fw-semibold small">{{ $update->author->name ?? '—' }}</span>
                                    <span class="badge bg-secondary-subtle text-secondary-emphasis" style="font-size:10px;">
                                        {{ ucfirst(str_replace('_', ' ', $update->author->role ?? '')) }}
                                    </span>
                                    @if($update->status)
                                        <span class="badge {{ $update->status_badge_class }}" style="font-size:10px;">
                                            → {{ $update->status_label }}
                                        </span>
                                    @endif
                                </div>
                                @if($update->note)
                                    <p class="mb-1 small">{{ $update->note }}</p>
                                @endif
                                <div class="text-muted" style="font-size:.72rem;">
                                    <i class="ri-time-line me-1"></i>{{ $update->created_at->format('d M Y, h:i A') }}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif

                {{-- Post Update Form --}}
                @if(!in_array($enquiry->status, ['resolved']))
                <div class="border-top pt-4">
                    <h6 class="text-muted mb-3 small text-uppercase fw-semibold">Post an Update</h6>

                    @if($errors->any())
                        <div class="alert alert-danger alert-sm py-2">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('provider.enquiries.updates.store', $enquiry) }}">
                        @csrf
                        <div class="mb-3">
                            <textarea name="note" class="form-control form-control-sm @error('note') is-invalid @enderror"
                                rows="3"
                                placeholder="Add a note or comment about this request...">{{ old('note') }}</textarea>
                        </div>

                        @if(!in_array($enquiry->status, ['completed', 'resolved']))
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Update Status (optional)</label>
                            <select name="status" class="form-select form-select-sm" style="max-width:240px;">
                                <option value="">— No status change —</option>
                                <option value="under_processing" {{ old('status') === 'under_processing' ? 'selected' : '' }}>
                                    Under Processing
                                </option>
                                <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>
                                    Completed
                                </option>
                            </select>
                        </div>
                        @endif

                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="ri-send-plane-line me-1"></i>Post Update
                        </button>
                    </form>
                </div>
                @else
                    <p class="text-muted small fst-italic mt-3 mb-0">This request has been resolved and closed. No further updates can be posted.</p>
                @endif

            </div>
        </div>

        <a href="{{ route('provider.enquiries.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="ri-arrow-left-line me-1"></i>Back to Service Requests
        </a>

    </div>
</div>
@endsection
