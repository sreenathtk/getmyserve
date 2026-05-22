@extends('staff.layouts.app')
@section('title', 'Service Request')
@section('page-title', 'Service Request Detail')

@section('content')
@php $hasCallAgent = auth()->user()->callAgent !== null; @endphp
<div class="row justify-content-center">
    <div class="col-lg-9">

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="ri-checkbox-circle-line me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        {{-- Enquiry Details + Status Update --}}
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h5 class="card-title mb-1">{{ $enquiry->full_name }}</h5>
                        <small class="text-muted">Submitted {{ $enquiry->created_at->format('d M Y, h:i A') }}</small>
                    </div>
                    <form method="POST" action="{{ route('staff.enquiries.update-status', $enquiry) }}"
                          class="d-flex gap-2 align-items-center">
                        @csrf @method('PATCH')
                        <select name="status" class="form-select form-select-sm" style="width:auto;">
                            <option value="pending"          {{ $enquiry->status === 'pending'          ? 'selected' : '' }}>Pending</option>
                            <option value="in_progress"      {{ $enquiry->status === 'in_progress'      ? 'selected' : '' }}>In Progress</option>
                            <option value="under_processing" {{ $enquiry->status === 'under_processing' ? 'selected' : '' }}>Under Processing</option>
                            <option value="completed"        {{ $enquiry->status === 'completed'        ? 'selected' : '' }}>Completed</option>
                            <option value="resolved"         {{ $enquiry->status === 'resolved'         ? 'selected' : '' }}>Resolved</option>
                        </select>
                        <button type="submit" class="btn btn-sm btn-primary">Update</button>
                    </form>
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
                            <div class="text-muted small mb-1">Current Status</div>
                            @if($enquiry->status === 'pending')
                                <span class="badge bg-warning-subtle text-warning-emphasis fs-6">Pending</span>
                            @elseif($enquiry->status === 'in_progress')
                                <span class="badge bg-info-subtle text-info-emphasis fs-6">In Progress</span>
                            @elseif($enquiry->status === 'under_processing')
                                <span class="badge bg-primary-subtle text-primary-emphasis fs-6">Under Processing</span>
                            @elseif($enquiry->status === 'completed')
                                <span class="badge badge-soft-teal fs-6">Completed</span>
                            @else
                                <span class="badge bg-success-subtle text-success-emphasis fs-6">Resolved</span>
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
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="fw-semibold">{{ $enquiry->phone }}</div>
                                @if($hasCallAgent && $enquiry->phone)
                                <button class="btn btn-sm btn-success ms-2"
                                        title="Call customer"
                                        onclick="ziwoCall('{{ $enquiry->phone }}', 'enquiry', {{ $enquiry->id }})">
                                    <i class="ri-phone-line"></i>
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    @if($enquiry->whatsapp)
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <div class="text-muted small mb-1">WhatsApp</div>
                            <div class="fw-semibold">{{ $enquiry->whatsapp }}</div>
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
                    @if($hasCallAgent && $enquiry->assignedSp?->serviceProvider?->primary_contact_mobile)
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted small mb-1">Service Provider</div>
                                <div class="fw-semibold">{{ $enquiry->assignedSp->name }}</div>
                                <div class="text-muted small">{{ $enquiry->assignedSp->serviceProvider->primary_contact_mobile }}</div>
                            </div>
                            <button class="btn btn-sm btn-success ms-2 flex-shrink-0"
                                    title="Call service provider"
                                    onclick="ziwoCall('{{ $enquiry->assignedSp->serviceProvider->primary_contact_mobile }}', 'enquiry', {{ $enquiry->id }})">
                                <i class="ri-phone-line me-1"></i>Call SP
                            </button>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Service Quote --}}
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3 d-flex align-items-center gap-2">
                    <i class="ri-price-tag-3-line text-primary"></i>
                    Service Quote
                </h5>

                @if($enquiry->isQuoted())
                <div class="p-3 bg-light rounded mb-3 d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small mb-1">Quoted Price</div>
                        <div class="fw-bold fs-5">AED {{ number_format($enquiry->quoted_price, 2) }}</div>
                        @if($enquiry->quotedBy)
                        <div class="text-muted small mt-1">
                            Set by {{ $enquiry->quotedBy->name }}
                            @if($enquiry->quoted_at)
                                on {{ $enquiry->quoted_at->format('d M Y, h:i A') }}
                            @endif
                        </div>
                        @endif
                    </div>
                    <span class="badge {{ $enquiry->isPaid() ? 'bg-success' : 'bg-warning text-dark' }}">
                        {{ $enquiry->isPaid() ? 'Paid' : 'Awaiting Payment' }}
                    </span>
                </div>
                @endif

                @if(!$enquiry->isPaid())
                <form method="POST" action="{{ route('staff.enquiries.update-price', $enquiry) }}"
                      class="d-flex gap-2 align-items-center">
                    @csrf @method('PATCH')
                    <div class="input-group" style="max-width:260px;">
                        <span class="input-group-text">AED</span>
                        <input type="number" name="quoted_price" class="form-control form-control-sm"
                               step="0.01" min="0.01" placeholder="0.00"
                               value="{{ $enquiry->quoted_price }}">
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary">
                        {{ $enquiry->isQuoted() ? 'Update Price' : 'Set Price' }}
                    </button>
                </form>
                @endif
            </div>
        </div>

        {{-- Payment Status (read-only for staff) --}}
        @if($enquiry->isPaid() || $enquiry->payment_status === 'refunded')
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3 d-flex align-items-center gap-2">
                    <i class="ri-secure-payment-line text-primary"></i>
                    Payment
                </h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <div class="text-muted small mb-1">Paid Amount</div>
                            <div class="fw-bold">AED {{ number_format($enquiry->paid_amount, 2) }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <div class="text-muted small mb-1">Payment Status</div>
                            <span class="badge {{ $enquiry->payment_status === 'refunded' ? 'bg-danger' : 'bg-success' }} fs-6">
                                {{ ucfirst($enquiry->payment_status) }}
                            </span>
                        </div>
                    </div>
                    @if($enquiry->paid_at)
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <div class="text-muted small mb-1">Paid At</div>
                            <div class="fw-semibold">{{ $enquiry->paid_at->format('d M Y, h:i A') }}</div>
                        </div>
                    </div>
                    @endif
                    @if($enquiry->refunded_amount > 0)
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <div class="text-muted small mb-1">Refunded Amount</div>
                            <div class="fw-semibold text-danger">AED {{ number_format($enquiry->refunded_amount, 2) }}</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- Related Files (read-only for staff) --}}
        @if($enquiry->files->isNotEmpty())
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-4 d-flex align-items-center gap-2">
                    <i class="ri-folder-open-line text-primary"></i>
                    Related Files
                    <span class="badge bg-primary-subtle text-primary-emphasis ms-1">{{ $enquiry->files->count() }}</span>
                </h5>
                @foreach($enquiry->files as $file)
                <div class="d-flex align-items-center gap-3 p-3 mb-2 rounded"
                     style="border:1px solid #e4e6ef;background:#fafafa;">
                    <i class="{{ $file->icon_class }}" style="font-size:1.5rem;flex-shrink:0;"></i>
                    <div class="flex-grow-1 min-w-0">
                        <div class="fw-semibold text-truncate">{{ $file->file_name }}</div>
                        <div class="text-muted small">
                            {{ $file->original_name }}
                            &nbsp;·&nbsp; {{ $file->formatted_size }}
                            &nbsp;·&nbsp; {{ $file->created_at->format('d M Y, h:i A') }}
                        </div>
                    </div>
                    <a href="{{ Storage::url($file->file_path) }}"
                       target="_blank"
                       class="btn btn-sm btn-outline-primary flex-shrink-0" title="Download">
                        <i class="ri-download-line"></i>
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

                <div class="border-top pt-4">
                    <h6 class="text-muted mb-3 small text-uppercase fw-semibold">Post an Update</h6>
                    @error('note') <div class="alert alert-danger py-2 small">{{ $message }}</div> @enderror
                    <form method="POST" action="{{ route('staff.enquiries.updates.store', $enquiry) }}">
                        @csrf
                        <div class="mb-3">
                            <textarea name="note" class="form-control form-control-sm"
                                rows="3" placeholder="Add a note or comment...">{{ old('note') }}</textarea>
                        </div>
                        <div class="d-flex gap-2 align-items-center flex-wrap">
                            <select name="status" class="form-select form-select-sm" style="max-width:220px;">
                                <option value="">— No status change —</option>
                                <option value="pending">Pending</option>
                                <option value="in_progress">In Progress</option>
                                <option value="under_processing">Under Processing</option>
                                <option value="completed">Completed</option>
                                <option value="resolved">Resolved</option>
                            </select>
                            @if($enquiry->assigned_sp_id)
                            <div class="form-check mb-0">
                                <input class="form-check-input" type="checkbox" name="notify_sp" id="notify_sp" value="1">
                                <label class="form-check-label" for="notify_sp" style="font-size:12px;">
                                    <i class="ri-whatsapp-line" style="color:#25d366;"></i> Notify SP via WhatsApp
                                </label>
                            </div>
                            @endif
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="ri-send-plane-line me-1"></i>Post Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Activity Log --}}
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-4 d-flex align-items-center gap-2">
                    <i class="ri-history-line text-primary"></i>
                    Activity Log
                </h5>

                <div class="row g-4">

                    {{-- Status History --}}
                    <div class="col-md-6">
                        <h6 class="text-muted small text-uppercase fw-semibold mb-3">Status History</h6>
                        @if($enquiry->statusLogs->isEmpty())
                            <p class="text-muted small">No status changes yet.</p>
                        @else
                            <div class="timeline-list">
                                @foreach($enquiry->statusLogs as $log)
                                <div class="d-flex gap-3 mb-3">
                                    <div class="flex-shrink-0 mt-1">
                                        <span class="badge {{ $log->badge_class }} rounded-pill">&nbsp;</span>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">
                                            <span class="badge {{ $log->badge_class }}">{{ $log->label }}</span>
                                        </div>
                                        <div class="small text-muted mt-1">
                                            <i class="ri-user-line me-1"></i>{{ $log->changedBy->name ?? '—' }}
                                            <span class="text-muted">({{ $log->changedBy->role ?? '' }})</span>
                                        </div>
                                        <div class="small text-muted">
                                            <i class="ri-time-line me-1"></i>{{ $log->created_at->format('d M Y, h:i A') }}
                                        </div>
                                        <div class="small text-muted">
                                            Changed from:
                                            <span class="fw-semibold">{{ ucfirst(str_replace('_', ' ', $log->old_status)) }}</span>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Viewed By --}}
                    <div class="col-md-6">
                        <h6 class="text-muted small text-uppercase fw-semibold mb-3">Viewed By</h6>
                        @if($enquiry->viewLogs->isEmpty())
                            <p class="text-muted small">No views recorded.</p>
                        @else
                            <div style="max-height:320px;overflow-y:auto;">
                                @foreach($enquiry->viewLogs as $view)
                                <div class="d-flex align-items-center gap-2 mb-2 p-2 rounded" style="background:#f8f9fa;">
                                    <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary text-white flex-shrink-0"
                                         style="width:30px;height:30px;font-size:.75rem;font-weight:700;">
                                        {{ strtoupper(substr($view->viewer->name ?? '?', 0, 1)) }}
                                    </div>
                                    <div class="flex-grow-1 min-w-0">
                                        <div class="fw-semibold small text-truncate">{{ $view->viewer->name ?? '—' }}</div>
                                        <div class="text-muted" style="font-size:.72rem;">
                                            {{ $view->created_at->format('d M Y, h:i A') }}
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>

        <a href="{{ route('staff.enquiries.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="ri-arrow-left-line me-1"></i>Back to Service Requests
        </a>

    </div>
</div>
@endsection

@section('scripts')
<script>
function ziwoCall(phone, entityType, entityId) {
    window.dispatchEvent(new CustomEvent('ziwo:dial', {
        detail: { phone, entity_type: entityType, entity_id: entityId }
    }));
}
</script>
@endsection
