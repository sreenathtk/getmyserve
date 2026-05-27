@extends('admin.layouts.app')
@section('title', 'Payment #' . $payment->id)
@section('page-title', 'Payment Detail')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">

        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <h5 class="card-title mb-1">Payment #{{ $payment->id }}</h5>
                        <small class="text-muted">{{ $payment->created_at->format('d M Y, h:i A') }}</small>
                    </div>
                    @if($payment->type === 'charge')
                        <span class="badge badge-soft-success fs-6">Payment</span>
                    @else
                        <span class="badge badge-soft-warning fs-6">Refund</span>
                    @endif
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <div class="text-muted small mb-1">Amount</div>
                            <div class="fw-bold fs-4 {{ $payment->type === 'refund' ? 'text-danger' : 'text-success' }}">
                                {{ $payment->type === 'refund' ? '−' : '' }}AED {{ number_format($payment->amount, 2) }}
                            </div>
                            <div class="text-muted small">{{ $payment->currency }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <div class="text-muted small mb-1">Status</div>
                            <span class="badge {{ $payment->getStatusBadgeClass() }} fs-6">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="p-3 bg-light rounded">
                            <div class="text-muted small mb-1">Source</div>
                            @if($payment->order_id)
                                <a href="{{ route('admin.orders.show', $payment->order) }}" class="fw-semibold text-primary">
                                    Order #{{ str_pad($payment->order_id, 5, '0', STR_PAD_LEFT) }}
                                </a>
                                @if($payment->order?->user)
                                    &nbsp;·&nbsp; {{ $payment->order->user->name }} ({{ $payment->order->user->email }})
                                @endif
                            @elseif($payment->enquiry_id)
                                <a href="{{ route('admin.enquiries.show', $payment->enquiry) }}" class="fw-semibold text-primary">
                                    Request #{{ str_pad($payment->enquiry_id, 5, '0', STR_PAD_LEFT) }}
                                </a>
                                &nbsp;·&nbsp; {{ $payment->enquiry->full_name }} ({{ $payment->enquiry->email }})
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </div>
                    </div>
                    @if($payment->stripe_payment_intent_id)
                    <div class="col-12">
                        <div class="p-3 bg-light rounded">
                            <div class="text-muted small mb-1">Stripe Payment Intent ID</div>
                            <code>{{ $payment->stripe_payment_intent_id }}</code>
                        </div>
                    </div>
                    @endif
                    @if($payment->stripe_refund_id)
                    <div class="col-12">
                        <div class="p-3 bg-light rounded">
                            <div class="text-muted small mb-1">Stripe Refund ID</div>
                            <code>{{ $payment->stripe_refund_id }}</code>
                        </div>
                    </div>
                    @endif
                    @if($payment->metadata)
                    <div class="col-12">
                        <div class="p-3 bg-light rounded">
                            <div class="text-muted small mb-1">Metadata</div>
                            <pre class="mb-0 small">{{ json_encode($payment->metadata, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        @if($payment->order)
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3 d-flex align-items-center gap-2">
                    <i class="ri-shopping-bag-line text-primary"></i>
                    Order Items
                </h5>
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
                            @php
                                $displayItems = $payment->order->orderItems->isNotEmpty()
                                    ? $payment->order->orderItems
                                    : collect($payment->order->items ?? [])->map(fn($i) => (object) $i);
                            @endphp
                            @forelse($displayItems as $item)
                            <tr>
                                <td>{{ $item->service_name }}</td>
                                <td>{{ ucfirst($item->package ?? 'standard') }}</td>
                                <td class="text-end">AED {{ number_format($item->price ?? 0, 2) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center text-muted">No items</td></tr>
                            @endforelse
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="2" class="text-end fw-semibold">Total</td>
                                <td class="text-end fw-bold">AED {{ number_format($payment->order->amount, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        @elseif($payment->enquiry)
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3 d-flex align-items-center gap-2">
                    <i class="ri-file-list-3-line text-primary"></i>
                    Service Request
                </h5>
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <a href="{{ route('admin.enquiries.show', $payment->enquiry) }}" class="fw-semibold text-primary fs-6">
                            Request #{{ str_pad($payment->enquiry->id, 5, '0', STR_PAD_LEFT) }}
                        </a>
                        @if($payment->enquiry->service)
                        <div class="text-muted small mt-1">
                            <i class="ri-stack-line me-1"></i>{{ $payment->enquiry->service->name }}
                        </div>
                        @endif
                        <div class="text-muted small mt-1">
                            <i class="ri-user-line me-1"></i>{{ $payment->enquiry->full_name }}
                            &nbsp;·&nbsp; {{ $payment->enquiry->email }}
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="text-muted small mb-1">Quoted Price</div>
                        <div class="fw-bold">AED {{ number_format($payment->enquiry->quoted_price ?? $payment->amount, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="ri-arrow-left-line me-1"></i>Back to Payments
        </a>

    </div>
</div>
@endsection
