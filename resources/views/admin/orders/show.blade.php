@extends('admin.layouts.app')
@section('title', 'Order #' . str_pad($order->id, 6, '0', STR_PAD_LEFT))
@section('page-title', 'Order Detail')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="ri-checkbox-circle-line me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="ri-error-warning-line me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if($order->status === 'cancel_requested')
        <div class="alert alert-warning d-flex justify-content-between align-items-center mb-4">
            <div>
                <i class="ri-error-warning-line me-2"></i>
                <strong>Cancellation Requested</strong> — The customer has requested to cancel this order.
            </div>
            <form method="POST" action="{{ route('admin.orders.accept-cancellation', $order) }}" class="m-0 ms-3">
                @csrf
                <button type="submit" class="btn btn-sm btn-danger"
                        onclick="return confirm('Accept this cancellation and mark order as cancelled?')">
                    <i class="ri-check-line me-1"></i>Accept Cancellation
                </button>
            </form>
        </div>
        @endif

        {{-- Order Header --}}
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h5 class="card-title mb-1">
                            Order #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                        </h5>
                        <small class="text-muted">
                            Placed {{ $order->created_at->format('d M Y, h:i A') }}
                        </small>
                    </div>
                    <form method="POST" action="{{ route('admin.orders.update-status', $order) }}"
                          class="d-flex gap-2 align-items-center">
                        @csrf @method('PATCH')
                        <select name="status" class="form-select form-select-sm" style="width:auto;">
                            @foreach(['pending','paid','processing','completed','cancel_requested','cancelled','failed'] as $s)
                                <option value="{{ $s }}" {{ $order->status === $s ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $s)) }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-sm btn-primary">Update</button>
                    </form>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded">
                            <div class="text-muted small mb-1">Customer</div>
                            <div class="fw-semibold">{{ $order->user?->name ?? '—' }}</div>
                            <div class="text-muted small">{{ $order->user?->email }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded">
                            <div class="text-muted small mb-1">Amount</div>
                            <div class="fw-semibold fs-5">AED {{ number_format($order->amount, 2) }}</div>
                            @if($order->refunded_amount > 0)
                                <div class="text-danger small">
                                    Refunded: AED {{ number_format($order->refunded_amount, 2) }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded">
                            <div class="text-muted small mb-1">Status</div>
                            <span class="badge {{ $order->getStatusBadgeClass() }} fs-6">
                                {{ ucfirst($order->status) }}
                            </span>
                            @if($order->refund_status !== 'none')
                                <br>
                                <span class="badge {{ $order->getRefundStatusBadgeClass() }} mt-1">
                                    Refund: {{ ucfirst($order->refund_status) }}
                                </span>
                            @endif
                        </div>
                    </div>
                    @if($order->stripe_payment_intent_id)
                    <div class="col-12">
                        <div class="p-3 bg-light rounded">
                            <div class="text-muted small mb-1">Stripe Payment Intent</div>
                            <code class="small">{{ $order->stripe_payment_intent_id }}</code>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Applied Offers --}}
        @if($order->offer_snapshot)
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3 d-flex align-items-center gap-2">
                    <i class="ri-price-tag-3-line text-primary"></i>
                    Applied Offers
                    <span class="badge bg-success-subtle text-success-emphasis ms-1">
                        {{ count($order->offer_snapshot) }}
                    </span>
                </h5>
                @foreach($order->offer_snapshot as $snap)
                <div class="d-flex justify-content-between align-items-center p-3 rounded mb-2"
                     style="background:linear-gradient(90deg,#f0fdf4,#eff6ff);border:1px solid #bbf7d0;">
                    <div>
                        <div class="fw-semibold">{{ $snap['title'] }}</div>
                        <div class="text-muted small mt-1">
                            Original: AED {{ number_format($snap['original_price'], 2) }}
                            &nbsp;→&nbsp; Offer price: AED {{ number_format($snap['offer_price'], 2) }}
                        </div>
                    </div>
                    <div class="fw-bold text-success ms-3" style="white-space:nowrap;">
                        − AED {{ number_format($snap['discount'], 2) }}
                    </div>
                </div>
                @endforeach

                <div class="d-flex justify-content-between small text-muted pt-2 border-top">
                    <span>Total discount applied</span>
                    <strong class="text-success">− AED {{ number_format($order->discount_amount, 2) }}</strong>
                </div>
            </div>
        </div>
        @endif

        {{-- Order Items --}}
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3 d-flex align-items-center gap-2">
                    <i class="ri-shopping-bag-line text-primary"></i>
                    Order Items
                </h5>

                @php
                    $orderItems  = $order->orderItems;
                    $comboGroups = $orderItems->whereNotNull('combo_offer_id')->groupBy('combo_offer_id');
                    $soloItems   = $orderItems->whereNull('combo_offer_id');
                    $useLegacy   = $orderItems->isEmpty();
                @endphp

                @if($useLegacy)
                    {{-- Legacy JSON fallback --}}
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm mb-0">
                            <thead class="table-light">
                                <tr><th>Service</th><th>Package</th><th>Price</th></tr>
                            </thead>
                            <tbody>
                                @foreach($order->items ?? [] as $item)
                                <tr>
                                    <td class="fw-semibold">{{ $item['service_name'] }}</td>
                                    <td>{{ ucfirst($item['package'] ?? 'standard') }}</td>
                                    <td>AED {{ number_format($item['price'] ?? 0, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    {{-- Combo groups --}}
                    @foreach($comboGroups as $offerId => $groupItems)
                        @php $first = $groupItems->first(); $allCancelled = $groupItems->every(fn($i) => $i->status === 'cancelled'); @endphp
                        <div class="mb-3 rounded overflow-hidden" style="border:2px solid #5664d2;">
                            <div class="d-flex justify-content-between align-items-center px-3 py-2"
                                 style="background:linear-gradient(90deg,#5664d2,#4f5fbc);color:#fff;">
                                <div class="fw-semibold small">
                                    <i class="ri-price-tag-3-line me-1"></i>
                                    Combo: {{ $first->combo_offer_title ?? 'Bundle' }}
                                    @if($allCancelled)
                                        <span class="badge bg-danger ms-2" style="font-size:.7rem;">Cancelled</span>
                                    @endif
                                </div>
                                <span class="small" style="opacity:.85;">
                                    Offer price: AED {{ number_format($first->combo_offer_price ?? $groupItems->sum('price'), 2) }}
                                </span>
                            </div>
                            <table class="table table-sm mb-0">
                                <thead class="table-light">
                                    <tr><th>Service</th><th>Package</th><th>Original Price</th><th>Status</th></tr>
                                </thead>
                                <tbody>
                                    @foreach($groupItems as $item)
                                    <tr class="{{ $item->status === 'cancelled' ? 'text-muted' : '' }}">
                                        <td class="{{ $item->status === 'cancelled' ? 'text-decoration-line-through' : 'fw-semibold' }}">
                                            {{ $item->service_name }}
                                        </td>
                                        <td>{{ ucfirst($item->package ?? 'standard') }}</td>
                                        <td>AED {{ number_format($item->price ?? 0, 2) }}</td>
                                        <td>
                                            @if($item->status === 'cancelled')
                                                <span class="badge badge-soft-danger">Cancelled</span>
                                            @else
                                                <span class="badge badge-soft-success">Active</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endforeach

                    {{-- Solo items --}}
                    @if($soloItems->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm mb-0">
                            <thead class="table-light">
                                <tr><th>Service</th><th>Package</th><th>Price</th><th>Original</th><th>Status</th></tr>
                            </thead>
                            <tbody>
                                @foreach($soloItems as $item)
                                <tr class="{{ $item->status === 'cancelled' ? 'text-muted' : '' }}">
                                    <td class="{{ $item->status === 'cancelled' ? 'fw-semibold text-decoration-line-through' : 'fw-semibold' }}">
                                        {{ $item->service_name }}
                                    </td>
                                    <td>{{ ucfirst($item->package ?? 'standard') }}</td>
                                    <td>
                                        @if($item->price)
                                            AED {{ number_format($item->price, 2) }}
                                        @else
                                            <span class="text-muted">On Enquiry</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->original_price && $item->original_price != $item->price)
                                            <span class="text-muted text-decoration-line-through">
                                                AED {{ number_format($item->original_price, 2) }}
                                            </span>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->status === 'cancelled')
                                            <span class="badge badge-soft-danger">Cancelled</span>
                                        @else
                                            <span class="badge badge-soft-success">Active</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                @endif

                <div class="d-flex justify-content-end gap-4 pt-3 border-top mt-3 small fw-semibold">
                    @if($order->discount_amount > 0)
                    <span class="text-muted">
                        Subtotal: AED {{ number_format($order->amount - $order->vat_amount + $order->discount_amount, 2) }}
                    </span>
                    <span class="text-success">
                        Discount: − AED {{ number_format($order->discount_amount, 2) }}
                    </span>
                    @endif
                    @if($order->vat_amount > 0)
                    <span class="text-muted">
                        VAT ({{ $order->vat_rate }}%): + AED {{ number_format($order->vat_amount, 2) }}
                    </span>
                    @endif
                    <span>Total: AED {{ number_format($order->amount, 2) }}</span>
                </div>
            </div>
        </div>

        {{-- Payment History --}}
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3 d-flex align-items-center gap-2">
                    <i class="ri-bank-card-line text-primary"></i>
                    Payment History
                    @if($order->payments->isNotEmpty())
                        <span class="badge bg-primary-subtle text-primary-emphasis ms-1">
                            {{ $order->payments->count() }}
                        </span>
                    @endif
                </h5>

                @if($order->payments->isEmpty())
                    <p class="text-muted small fst-italic">No payment records yet.</p>
                @else
                    @foreach($order->payments as $payment)
                    <div class="d-flex align-items-center gap-3 p-3 mb-2 rounded"
                         style="border:1px solid #e4e6ef;background:#fafafa;">
                        <div class="flex-shrink-0">
                            @if($payment->type === 'charge')
                                <i class="ri-secure-payment-line text-success" style="font-size:1.5rem;"></i>
                            @else
                                <i class="ri-refund-2-line text-warning" style="font-size:1.5rem;"></i>
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold">
                                {{ $payment->getTypeLabel() }}
                                — AED {{ number_format($payment->amount, 2) }}
                            </div>
                            <div class="text-muted small">
                                <span class="badge {{ $payment->getStatusBadgeClass() }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                                &nbsp;·&nbsp; {{ $payment->created_at->format('d M Y, h:i A') }}
                                @if($payment->stripe_payment_intent_id)
                                    &nbsp;·&nbsp; <code>{{ $payment->stripe_payment_intent_id }}</code>
                                @endif
                                @if($payment->stripe_refund_id)
                                    &nbsp;·&nbsp; <code>{{ $payment->stripe_refund_id }}</code>
                                @endif
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('admin.payments.show', $payment) }}"
                               class="btn btn-sm btn-outline-secondary">
                                <i class="ri-eye-line"></i>
                            </a>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>

        {{-- Refund --}}
        @if($order->isRefundable())
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3 d-flex align-items-center gap-2">
                    <i class="ri-refund-2-line text-danger"></i>
                    Process Refund
                    @if($order->refund_status === 'requested')
                        <span class="badge badge-soft-warning ms-1">Customer Requested</span>
                    @endif
                </h5>

                <p class="text-muted small mb-3">
                    Refundable amount:
                    <strong>AED {{ number_format($order->getRefundableAmount(), 2) }}</strong>
                    (paid AED {{ number_format($order->amount, 2) }},
                    refunded AED {{ number_format($order->refunded_amount, 2) }})
                </p>

                <form method="POST" action="{{ route('admin.orders.refund', $order) }}"
                      onsubmit="return confirm('Process this refund? This action cannot be undone.')">
                    @csrf
                    <div class="row g-2 align-items-end">
                        <div class="col-sm-3">
                            <label class="form-label small text-muted mb-1">Refund Amount (AED)</label>
                            <input type="number" name="refund_amount" class="form-control form-control-sm"
                                   min="0.01" max="{{ $order->getRefundableAmount() }}" step="0.01"
                                   value="{{ $order->getRefundableAmount() }}" required>
                        </div>
                        <div class="col-sm-5">
                            <label class="form-label small text-muted mb-1">Reason (optional)</label>
                            <input type="text" name="reason" class="form-control form-control-sm"
                                   placeholder="Reason for refund…">
                        </div>
                        <div class="col-sm-auto">
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="ri-refund-2-line me-1"></i> Process Refund
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @endif

        {{-- Assign Staff --}}
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3 d-flex align-items-center gap-2">
                    <i class="ri-user-star-line text-primary"></i>
                    Assigned Staff
                </h5>

                @if($order->assignedStaff)
                <div class="d-flex align-items-center gap-3 p-3 rounded mb-3"
                     style="border:1px solid #e4e6ef;background:#f0fdf4;">
                    <div class="d-flex align-items-center justify-content-center rounded-circle bg-success text-white flex-shrink-0"
                         style="width:38px;height:38px;font-size:.85rem;font-weight:700;">
                        {{ strtoupper(substr($order->assignedStaff->name, 0, 1)) }}
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold">{{ $order->assignedStaff->name }}</div>
                        <div class="text-muted small">{{ $order->assignedStaff->email }}</div>
                    </div>
                    <form method="POST" action="{{ route('admin.orders.assign-staff', $order) }}" class="m-0">
                        @csrf @method('PATCH')
                        <input type="hidden" name="staff_id" value="">
                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                onclick="return confirm('Remove staff assignment?')">
                            <i class="ri-link-unlink me-1"></i>Unassign
                        </button>
                    </form>
                </div>
                @else
                <p class="text-muted small mb-3">No staff member assigned yet.</p>
                @endif

                <form method="POST" action="{{ route('admin.orders.assign-staff', $order) }}"
                      class="d-flex gap-2 align-items-center">
                    @csrf @method('PATCH')
                    <select name="staff_id" class="form-select form-select-sm" style="max-width:340px;" required>
                        <option value="">— Select staff member —</option>
                        @foreach($staff as $member)
                        <option value="{{ $member->id }}"
                            {{ $order->assigned_staff_id == $member->id ? 'selected' : '' }}>
                            {{ $member->name }} ({{ $member->email }})
                        </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="ri-user-add-line me-1"></i>
                        {{ $order->assignedStaff ? 'Change' : 'Assign' }} Staff
                    </button>
                </form>
            </div>
        </div>

        {{-- Assign Service Provider --}}
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3 d-flex align-items-center gap-2">
                    <i class="ri-user-settings-line text-primary"></i>
                    Assigned Service Provider
                </h5>

                @if($order->assignedSp)
                <div class="d-flex align-items-center gap-3 p-3 rounded mb-3"
                     style="border:1px solid #e4e6ef;background:#f0fdf4;">
                    <div class="d-flex align-items-center justify-content-center rounded-circle bg-teal text-white flex-shrink-0"
                         style="width:38px;height:38px;font-size:.85rem;font-weight:700;background:#0d9488;">
                        {{ strtoupper(substr($order->assignedSp->name, 0, 1)) }}
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold">{{ $order->assignedSp->name }}</div>
                        <div class="text-muted small">{{ $order->assignedSp->email }}</div>
                    </div>
                    <form method="POST" action="{{ route('admin.orders.assign-sp', $order) }}" class="m-0">
                        @csrf @method('PATCH')
                        <input type="hidden" name="sp_id" value="">
                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                onclick="return confirm('Remove service provider assignment?')">
                            <i class="ri-link-unlink me-1"></i>Unassign
                        </button>
                    </form>
                </div>
                @else
                <p class="text-muted small mb-3">No service provider assigned yet.</p>
                @endif

                <form method="POST" action="{{ route('admin.orders.assign-sp', $order) }}"
                      class="d-flex gap-2 align-items-center">
                    @csrf @method('PATCH')
                    <select name="sp_id" class="form-select form-select-sm" style="max-width:340px;" required>
                        <option value="">— Select service provider —</option>
                        @foreach($providers as $provider)
                        <option value="{{ $provider->id }}"
                            {{ $order->assigned_sp_id == $provider->id ? 'selected' : '' }}>
                            {{ $provider->name }} ({{ $provider->email }})
                        </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="ri-user-add-line me-1"></i>
                        {{ $order->assignedSp ? 'Change' : 'Assign' }} Provider
                    </button>
                </form>
            </div>
        </div>

        {{-- Files --}}
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-4 d-flex align-items-center gap-2">
                    <i class="ri-folder-open-line text-primary"></i>
                    Order Files
                    @if($order->files->isNotEmpty())
                        <span class="badge bg-primary-subtle text-primary-emphasis ms-1">
                            {{ $order->files->count() }}
                        </span>
                    @endif
                </h5>

                @if($order->files->isEmpty())
                <div class="text-muted text-center py-3 mb-4" style="border:2px dashed #e4e6ef;border-radius:10px;">
                    <i class="ri-folder-open-line" style="font-size:2rem;opacity:.3;"></i>
                    <p class="mb-0 mt-1 small">No files uploaded yet.</p>
                </div>
                @else
                <div class="mb-4">
                    @foreach($order->files as $file)
                    <div class="d-flex align-items-center gap-3 p-3 mb-2 rounded"
                         style="border:1px solid #e4e6ef;background:#fafafa;">
                        <i class="{{ $file->icon_class }}" style="font-size:1.6rem;flex-shrink:0;"></i>
                        <div class="flex-grow-1 min-w-0">
                            <div class="fw-semibold text-truncate">{{ $file->file_name }}</div>
                            <div class="text-muted small">
                                {{ $file->original_name }}
                                &nbsp;·&nbsp; {{ $file->formatted_size }}
                                &nbsp;·&nbsp; {{ $file->created_at->format('d M Y, h:i A') }}
                                @if($file->uploader)
                                    &nbsp;·&nbsp; by {{ $file->uploader->name }}
                                @endif
                            </div>
                        </div>
                        <div class="d-flex gap-1 flex-shrink-0">
                            <a href="{{ Storage::url($file->file_path) }}" target="_blank"
                               class="btn btn-sm btn-outline-primary">
                                <i class="ri-download-line"></i>
                            </a>
                            <form method="POST"
                                  action="{{ route('admin.orders.files.destroy', [$order, $file]) }}"
                                  class="m-0">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                        onclick="return confirm('Delete this file?')">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif

                <div class="border-top pt-4">
                    <h6 class="text-muted mb-3 small text-uppercase fw-semibold">Upload New Files</h6>
                    <form method="POST" action="{{ route('admin.orders.files.store', $order) }}"
                          enctype="multipart/form-data" id="upload-form">
                        @csrf
                        <div id="file-rows">
                            <div class="file-row d-flex gap-2 align-items-center mb-2">
                                <div class="flex-grow-1">
                                    <input type="text" name="files[0][name]"
                                           class="form-control form-control-sm"
                                           placeholder="File name / description" required>
                                </div>
                                <div style="min-width:260px;">
                                    <input type="file" name="files[0][file]"
                                           class="form-control form-control-sm" required>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger remove-row"
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

        {{-- Notes --}}
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3 d-flex align-items-center gap-2">
                    <i class="ri-sticky-note-line text-primary"></i>
                    Internal Notes
                </h5>
                <form method="POST" action="{{ route('admin.orders.save-notes', $order) }}">
                    @csrf @method('PATCH')
                    <textarea name="notes" class="form-control form-control-sm mb-3"
                              rows="4" placeholder="Internal notes about this order…">{{ $order->notes }}</textarea>
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="ri-save-line me-1"></i> Save Notes
                    </button>
                </form>
            </div>
        </div>

        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="ri-arrow-left-line me-1"></i>Back to Orders
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
            <input type="text" name="files[${rowIdx}][name]"
                   class="form-control form-control-sm"
                   placeholder="File name / description" required>
        </div>
        <div style="min-width:260px;">
            <input type="file" name="files[${rowIdx}][file]"
                   class="form-control form-control-sm" required>
        </div>
        <button type="button" class="btn btn-sm btn-outline-danger remove-row" style="flex-shrink:0;">
            <i class="ri-close-line"></i>
        </button>
    `;
    container.appendChild(row);
    rowIdx++;
});

document.getElementById('file-rows').addEventListener('click', function (e) {
    const btn = e.target.closest('.remove-row');
    if (!btn) return;
    if (document.querySelectorAll('#file-rows .file-row').length > 1) {
        btn.closest('.file-row').remove();
    }
});
</script>
@endsection
