@extends('admin.layouts.app')
@section('title', 'Enquiry Detail')
@section('page-title', 'Enquiry Detail')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="ri-checkbox-circle-line me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        {{-- Enquiry Details --}}
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h5 class="card-title mb-1">{{ $enquiry->full_name }}</h5>
                        <small class="text-muted">
                            Submitted {{ $enquiry->created_at->format('d M Y, h:i A') }}
                            @if($enquiry->customer)
                                &nbsp;·&nbsp;
                                <a href="{{ route('admin.customers.show', $enquiry->customer) }}" class="text-primary">
                                    <i class="ri-user-line me-1"></i>View Customer
                                </a>
                            @endif
                        </small>
                    </div>
                    <form method="POST" action="{{ route('admin.enquiries.update-status', $enquiry) }}" class="d-flex gap-2 align-items-center">
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
                            <div class="text-muted small mb-1">Status</div>
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
                            <div class="fw-semibold">{{ $enquiry->phone }}</div>
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
                </div>
            </div>
        </div>

        {{-- Link to Customer --}}
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3 d-flex align-items-center gap-2">
                    <i class="ri-user-follow-line text-primary"></i>
                    Linked Customer
                </h5>

                @if($enquiry->customer)
                <div class="d-flex align-items-center gap-3 p-3 rounded mb-3"
                     style="border:1px solid #e4e6ef;background:#f8f9fa;">
                    <div class="d-flex align-items-center justify-content-center rounded-circle bg-success text-white flex-shrink-0"
                         style="width:38px;height:38px;font-size:.85rem;font-weight:700;">
                        {{ strtoupper(substr($enquiry->customer->name, 0, 1)) }}
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold">{{ $enquiry->customer->name }}</div>
                        <div class="text-muted small">{{ $enquiry->customer->email }}</div>
                    </div>
                    <a href="{{ route('admin.customers.show', $enquiry->customer) }}"
                       class="btn btn-sm btn-outline-primary me-1">
                        <i class="ri-eye-line me-1"></i>View Customer
                    </a>
                    <form method="POST" action="{{ route('admin.enquiries.link-customer', $enquiry) }}" class="m-0">
                        @csrf @method('PATCH')
                        <input type="hidden" name="customer_id" value="">
                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                onclick="return confirm('Remove the customer link?')">
                            <i class="ri-link-unlink me-1"></i>Unlink
                        </button>
                    </form>
                </div>
                @else
                <p class="text-muted small mb-3">
                    This enquiry is not linked to any registered customer.
                    Select a customer below to link them.
                </p>
                @endif

                <form method="POST" action="{{ route('admin.enquiries.link-customer', $enquiry) }}"
                      class="d-flex gap-2 align-items-center">
                    @csrf @method('PATCH')
                    <select name="customer_id" class="form-select form-select-sm" style="max-width:340px;" required>
                        <option value="">— Select customer —</option>
                        @foreach($customers as $c)
                        <option value="{{ $c->id }}" {{ $enquiry->user_id == $c->id ? 'selected' : '' }}>
                            {{ $c->name }} ({{ $c->email }})
                        </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="ri-link me-1"></i>{{ $enquiry->customer ? 'Change' : 'Link' }} Customer
                    </button>
                </form>
            </div>
        </div>

        {{-- Assign Service Provider --}}
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3 d-flex align-items-center gap-2">
                    <i class="ri-building-2-line text-primary"></i>
                    Assigned Service Provider
                </h5>

                @if($enquiry->assignedSp)
                <div class="d-flex align-items-center gap-3 p-3 rounded mb-3"
                     style="border:1px solid #e4e6ef;background:#f0fdf4;">
                    <div class="d-flex align-items-center justify-content-center rounded-circle bg-success text-white flex-shrink-0"
                         style="width:38px;height:38px;font-size:.85rem;font-weight:700;">
                        {{ strtoupper(substr($enquiry->assignedSp->name, 0, 1)) }}
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold">{{ $enquiry->assignedSp->name }}</div>
                        <div class="text-muted small">{{ $enquiry->assignedSp->email }}</div>
                    </div>
                    <form method="POST" action="{{ route('admin.enquiries.assign-sp', $enquiry) }}" class="m-0">
                        @csrf @method('PATCH')
                        <input type="hidden" name="sp_id" value="">
                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                onclick="return confirm('Remove the service provider assignment?')">
                            <i class="ri-link-unlink me-1"></i>Unassign
                        </button>
                    </form>
                </div>
                @else
                <p class="text-muted small mb-3">No service provider assigned to this request yet.</p>
                @endif

                <form method="POST" action="{{ route('admin.enquiries.assign-sp', $enquiry) }}"
                      class="d-flex gap-2 align-items-center">
                    @csrf @method('PATCH')
                    <select name="sp_id" class="form-select form-select-sm" style="max-width:340px;" required>
                        <option value="">— Select service provider —</option>
                        @foreach($serviceProviders as $sp)
                        <option value="{{ $sp->id }}" {{ $enquiry->assigned_sp_id == $sp->id ? 'selected' : '' }}>
                            {{ $sp->name }} ({{ $sp->email }})
                        </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="ri-user-add-line me-1"></i>{{ $enquiry->assignedSp ? 'Change' : 'Assign' }} SP
                    </button>
                </form>
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
                <form method="POST" action="{{ route('admin.enquiries.update-price', $enquiry) }}"
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

        {{-- Payment --}}
        @if($enquiry->isPaid() || $enquiry->payment_status === 'refunded')
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3 d-flex align-items-center gap-2">
                    <i class="ri-secure-payment-line text-primary"></i>
                    Payment
                </h5>

                <div class="row g-3 mb-3">
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
                    @if($enquiry->stripe_payment_intent_id)
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <div class="text-muted small mb-1">Stripe Payment Intent</div>
                            <div class="fw-semibold small text-truncate" style="font-family:monospace;">
                                {{ $enquiry->stripe_payment_intent_id }}
                            </div>
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

                @if($enquiry->isRefundable())
                <div class="border-top pt-3">
                    <h6 class="text-muted small text-uppercase fw-semibold mb-2">Issue Refund</h6>
                    <form method="POST" action="{{ route('admin.enquiries.refund', $enquiry) }}"
                          class="d-flex gap-2 align-items-center flex-wrap"
                          onsubmit="return confirm('Are you sure you want to issue this refund?')">
                        @csrf
                        <div class="input-group" style="max-width:240px;">
                            <span class="input-group-text">AED</span>
                            <input type="number" name="refund_amount" class="form-control form-control-sm"
                                   step="0.01" min="0.01" max="{{ $enquiry->getRefundableAmount() }}"
                                   placeholder="Amount to refund">
                        </div>
                        <small class="text-muted">Max: AED {{ number_format($enquiry->getRefundableAmount(), 2) }}</small>
                        <button type="submit" class="btn btn-sm btn-danger">
                            <i class="ri-refund-2-line me-1"></i>Refund
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Related Files --}}
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-4 d-flex align-items-center gap-2">
                    <i class="ri-folder-open-line text-primary"></i>
                    Related Files
                    @if($enquiry->files->isNotEmpty())
                        <span class="badge bg-primary-subtle text-primary-emphasis ms-1">{{ $enquiry->files->count() }}</span>
                    @endif
                </h5>

                {{-- Existing files --}}
                @if($enquiry->files->isEmpty())
                <div class="text-muted text-center py-3 mb-4" style="border:2px dashed #e4e6ef;border-radius:10px;">
                    <i class="ri-folder-open-line" style="font-size:2rem;opacity:.3;"></i>
                    <p class="mb-0 mt-1 small">No files uploaded yet.</p>
                </div>
                @else
                <div class="mb-4">
                    @foreach($enquiry->files as $file)
                    <div class="d-flex align-items-center gap-3 p-3 mb-2 rounded"
                         style="border:1px solid #e4e6ef;background:#fafafa;">
                        <i class="{{ $file->icon_class }}" style="font-size:1.6rem;flex-shrink:0;"></i>
                        <div class="flex-grow-1 min-w-0">
                            <div class="fw-semibold text-truncate">{{ $file->file_name }}</div>
                            <div class="text-muted small">
                                {{ $file->original_name }}
                                &nbsp;·&nbsp; {{ $file->formatted_size }}
                                &nbsp;·&nbsp; {{ $file->created_at->format('d M Y, h:i A') }}
                            </div>
                        </div>
                        <div class="d-flex gap-1 flex-shrink-0">
                            <a href="{{ Storage::url($file->file_path) }}"
                               target="_blank"
                               class="btn btn-sm btn-outline-primary" title="Download">
                                <i class="ri-download-line"></i>
                            </a>
                            <form method="POST"
                                  action="{{ route('admin.enquiries.files.destroy', [$enquiry, $file]) }}"
                                  class="m-0">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="btn btn-sm btn-outline-danger"
                                        title="Delete"
                                        onclick="return confirm('Delete this file?')">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif

                {{-- Upload new files --}}
                <div class="border-top pt-4">
                    <h6 class="text-muted mb-3 small text-uppercase fw-semibold">Upload New Files</h6>
                    <form method="POST"
                          action="{{ route('admin.enquiries.files.store', $enquiry) }}"
                          enctype="multipart/form-data"
                          id="upload-form">
                        @csrf

                        <div id="file-rows">
                            {{-- Initial row (index 0) --}}
                            <div class="file-row d-flex gap-2 align-items-center mb-2">
                                <div class="flex-grow-1">
                                    <input type="text"
                                           name="files[0][name]"
                                           class="form-control form-control-sm"
                                           placeholder="File name / description"
                                           required>
                                </div>
                                <div style="min-width:260px;">
                                    <input type="file"
                                           name="files[0][file]"
                                           class="form-control form-control-sm"
                                           required>
                                </div>
                                <button type="button"
                                        class="btn btn-sm btn-outline-danger remove-row"
                                        title="Remove row"
                                        style="flex-shrink:0;">
                                    <i class="ri-close-line"></i>
                                </button>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <button type="button" id="add-row-btn" class="btn btn-sm btn-outline-secondary">
                                <i class="ri-add-line me-1"></i>Add Another File
                            </button>
                            <button type="submit" class="btn btn-sm btn-primary">
                                <i class="ri-upload-cloud-line me-1"></i>Upload Files
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>

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
                    <form method="POST" action="{{ route('admin.enquiries.updates.store', $enquiry) }}">
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
                            <p class="text-muted small fst-italic">No status changes recorded.</p>
                        @else
                            @foreach($enquiry->statusLogs as $log)
                            <div class="d-flex gap-3 mb-3">
                                <div class="flex-shrink-0" style="padding-top:3px;">
                                    <span class="badge rounded-pill {{ $log->badge_class }}">&nbsp;&nbsp;</span>
                                </div>
                                <div>
                                    <div><span class="badge {{ $log->badge_class }}">{{ $log->label }}</span></div>
                                    <div class="small text-muted mt-1">
                                        <i class="ri-user-line me-1"></i>
                                        <strong>{{ $log->changedBy->name ?? '—' }}</strong>
                                        <span class="text-muted">({{ ucfirst(str_replace('_', ' ', $log->changedBy->role ?? '')) }})</span>
                                    </div>
                                    <div class="small text-muted">
                                        <i class="ri-time-line me-1"></i>{{ $log->created_at->format('d M Y, h:i A') }}
                                    </div>
                                    <div class="small text-muted">
                                        From: <strong>{{ ucfirst(str_replace('_', ' ', $log->old_status)) }}</strong>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @endif
                    </div>

                    {{-- Viewed By --}}
                    <div class="col-md-6">
                        <h6 class="text-muted small text-uppercase fw-semibold mb-3">
                            Viewed By
                            @if($enquiry->viewLogs->isNotEmpty())
                                <span class="badge bg-secondary-subtle text-secondary-emphasis ms-1 fw-normal">
                                    {{ $enquiry->viewLogs->count() }} view(s)
                                </span>
                            @endif
                        </h6>
                        @if($enquiry->viewLogs->isEmpty())
                            <p class="text-muted small fst-italic">No staff views recorded.</p>
                        @else
                            <div style="max-height:340px;overflow-y:auto;">
                                @foreach($enquiry->viewLogs as $view)
                                <div class="d-flex align-items-center gap-2 mb-2 p-2 rounded" style="background:#f8f9fa;">
                                    <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary text-white flex-shrink-0"
                                         style="width:30px;height:30px;font-size:.75rem;font-weight:700;">
                                        {{ strtoupper(substr($view->viewer->name ?? '?', 0, 1)) }}
                                    </div>
                                    <div class="flex-grow-1 min-w-0">
                                        <div class="fw-semibold small text-truncate">{{ $view->viewer->name ?? '—' }}</div>
                                        <div class="small text-muted" style="font-size:.72rem;">
                                            {{ ucfirst(str_replace('_', ' ', $view->viewer->role ?? '')) }}
                                            &nbsp;·&nbsp; {{ $view->created_at->format('d M Y, h:i A') }}
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

        <a href="{{ route('admin.enquiries.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="ri-arrow-left-line me-1"></i>Back to Service Requests
        </a>

    </div>
</div>
@endsection

@section('scripts')
<script>
let rowIdx = 1;

document.getElementById('add-row-btn').addEventListener('click', function () {
    const container = document.getElementById('file-rows');
    const row = document.createElement('div');
    row.className = 'file-row d-flex gap-2 align-items-center mb-2';
    row.innerHTML = `
        <div class="flex-grow-1">
            <input type="text"
                   name="files[${rowIdx}][name]"
                   class="form-control form-control-sm"
                   placeholder="File name / description"
                   required>
        </div>
        <div style="min-width:260px;">
            <input type="file"
                   name="files[${rowIdx}][file]"
                   class="form-control form-control-sm"
                   required>
        </div>
        <button type="button"
                class="btn btn-sm btn-outline-danger remove-row"
                title="Remove row"
                style="flex-shrink:0;">
            <i class="ri-close-line"></i>
        </button>
    `;
    container.appendChild(row);
    rowIdx++;
});

document.getElementById('file-rows').addEventListener('click', function (e) {
    const btn = e.target.closest('.remove-row');
    if (!btn) return;
    const rows = document.querySelectorAll('#file-rows .file-row');
    if (rows.length > 1) {
        btn.closest('.file-row').remove();
    }
});
</script>
@endsection
