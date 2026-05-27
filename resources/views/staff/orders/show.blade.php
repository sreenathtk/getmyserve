@extends('staff.layouts.app')
@section('title', 'Order #' . str_pad($order->id, 6, '0', STR_PAD_LEFT))
@section('page-title', 'Order Detail')

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

        @if($order->status === 'cancel_requested')
        <div class="alert alert-warning d-flex justify-content-between align-items-center mb-4">
            <div>
                <i class="ri-error-warning-line me-2"></i>
                <strong>Cancellation Requested</strong> — The customer has requested to cancel this order.
            </div>
            <form method="POST" action="{{ route('staff.orders.accept-cancellation', $order) }}" class="m-0 ms-3">
                @csrf
                <button type="submit" class="btn btn-sm btn-danger"
                        onclick="return confirm('Accept this cancellation and mark order as cancelled?')">
                    <i class="ri-check-line me-1"></i>Accept Cancellation
                </button>
            </form>
        </div>
        @endif

        {{-- Header --}}
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h5 class="card-title mb-1">
                            Order #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                        </h5>
                        <small class="text-muted">{{ $order->created_at->format('d M Y, h:i A') }}</small>
                    </div>
                    @if($order->status !== 'cancel_requested')
                    <form method="POST" action="{{ route('staff.orders.update-status', $order) }}"
                          class="d-flex gap-2 align-items-center">
                        @csrf @method('PATCH')
                        <select name="status" class="form-select form-select-sm" style="width:auto;">
                            <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="completed"  {{ $order->status === 'completed'  ? 'selected' : '' }}>Completed</option>
                        </select>
                        <button type="submit" class="btn btn-sm btn-primary">Update</button>
                    </form>
                    @endif
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded">
                            <div class="text-muted small mb-1">Customer</div>
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="fw-semibold">{{ $order->user?->name ?? '—' }}</div>
                                    <div class="text-muted small">{{ $order->user?->email }}</div>
                                </div>
                                @if($hasCallAgent && $order->user?->phone)
                                <button class="btn btn-sm btn-success ms-2 flex-shrink-0"
                                        title="Call {{ $order->user->name }}"
                                        onclick="ziwoCall('{{ $order->user->phone }}', 'order', {{ $order->id }})">
                                    <i class="ri-phone-line"></i>
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded">
                            <div class="text-muted small mb-1">Amount</div>
                            <div class="fw-semibold fs-5">AED {{ number_format($order->amount, 2) }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded">
                            <div class="text-muted small mb-1">Status</div>
                            <span class="badge {{ $order->getStatusBadgeClass() }} fs-6">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Order Items --}}
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3 d-flex align-items-center gap-2">
                    <i class="ri-shopping-bag-line text-primary"></i>
                    Order Items
                </h5>
                @php
                    $displayItems = $order->orderItems->isNotEmpty()
                        ? $order->orderItems
                        : collect($order->items ?? [])->map(fn($i) => (object) $i);
                @endphp
                <div class="table-responsive">
                    <table class="table table-bordered table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Service</th>
                                <th>Package</th>
                                <th class="text-end">Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($displayItems as $item)
                            <tr>
                                <td class="fw-semibold">{{ $item->service_name }}</td>
                                <td>{{ ucfirst($item->package ?? 'standard') }}</td>
                                <td class="text-end">AED {{ number_format($item->price ?? 0, 2) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center text-muted">No items</td></tr>
                            @endforelse
                        </tbody>
                        <tfoot class="table-light">
                            @if($order->discount_amount > 0)
                            <tr>
                                <td colspan="2" class="text-end text-success">Discount</td>
                                <td class="text-end text-success">− AED {{ number_format($order->discount_amount, 2) }}</td>
                            </tr>
                            @endif
                            @if($order->vat_amount > 0)
                            <tr>
                                <td colspan="2" class="text-end text-muted">VAT ({{ $order->vat_rate }}%)</td>
                                <td class="text-end text-muted">+ AED {{ number_format($order->vat_amount, 2) }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td colspan="2" class="text-end fw-semibold">Total</td>
                                <td class="text-end fw-bold">AED {{ number_format($order->amount, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- Payment Summary --}}
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3 d-flex align-items-center gap-2">
                    <i class="ri-bank-card-line text-primary"></i>
                    Payment Summary
                </h5>
                @if($order->payments->isEmpty())
                    <p class="text-muted small fst-italic">No payment records.</p>
                @else
                    @foreach($order->payments as $payment)
                    <div class="d-flex align-items-center gap-3 p-3 mb-2 rounded"
                         style="border:1px solid #e4e6ef;background:#fafafa;">
                        <i class="{{ $payment->type === 'charge' ? 'ri-secure-payment-line text-success' : 'ri-refund-2-line text-warning' }}"
                           style="font-size:1.4rem;flex-shrink:0;"></i>
                        <div class="flex-grow-1">
                            <div class="fw-semibold">
                                {{ $payment->getTypeLabel() }} — AED {{ number_format($payment->amount, 2) }}
                            </div>
                            <div class="text-muted small">
                                <span class="badge {{ $payment->getStatusBadgeClass() }}">{{ ucfirst($payment->status) }}</span>
                                &nbsp;·&nbsp; {{ $payment->created_at->format('d M Y, h:i A') }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endif
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
                    <div class="d-flex align-items-center justify-content-center rounded-circle text-white flex-shrink-0"
                         style="width:38px;height:38px;font-size:.85rem;font-weight:700;background:#0d9488;">
                        {{ strtoupper(substr($order->assignedSp->name, 0, 1)) }}
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold">{{ $order->assignedSp->name }}</div>
                        <div class="text-muted small">{{ $order->assignedSp->email }}</div>
                    </div>
                    <div class="d-flex gap-2">
                        @if($hasCallAgent && $order->assignedSp?->serviceProvider?->primary_contact_mobile)
                        <button class="btn btn-sm btn-success"
                                title="Call {{ $order->assignedSp->name }}"
                                onclick="ziwoCall('{{ $order->assignedSp->serviceProvider->primary_contact_mobile }}', 'order', {{ $order->id }})">
                            <i class="ri-phone-line me-1"></i>Call SP
                        </button>
                        @endif
                        <form method="POST" action="{{ route('staff.orders.assign-sp', $order) }}" class="m-0">
                            @csrf @method('PATCH')
                            <input type="hidden" name="sp_id" value="">
                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('Remove service provider assignment?')">
                                <i class="ri-link-unlink me-1"></i>Unassign
                            </button>
                        </form>
                    </div>
                </div>
                @else
                <p class="text-muted small mb-3">No service provider assigned yet.</p>
                @endif

                <form method="POST" action="{{ route('staff.orders.assign-sp', $order) }}"
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
        @if($order->files->isNotEmpty())
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3 d-flex align-items-center gap-2">
                    <i class="ri-folder-open-line text-primary"></i>
                    Order Files
                    <span class="badge bg-primary-subtle text-primary-emphasis ms-1">{{ $order->files->count() }}</span>
                </h5>
                @foreach($order->files as $file)
                <div class="d-flex align-items-center gap-3 p-3 mb-2 rounded"
                     style="border:1px solid #e4e6ef;background:#fafafa;">
                    <i class="{{ $file->icon_class }}" style="font-size:1.5rem;flex-shrink:0;"></i>
                    <div class="flex-grow-1 min-w-0">
                        <div class="fw-semibold text-truncate">{{ $file->file_name }}</div>
                        <div class="text-muted small">{{ $file->original_name }} · {{ $file->formatted_size }}</div>
                    </div>
                    <a href="{{ Storage::url($file->file_path) }}" target="_blank"
                       class="btn btn-sm btn-outline-primary flex-shrink-0">
                        <i class="ri-download-line"></i>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <a href="{{ route('staff.orders.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="ri-arrow-left-line me-1"></i>Back to Orders
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
