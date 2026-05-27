@extends('layouts.app')

@section('title', 'Order #' . str_pad($order->id, 6, '0', STR_PAD_LEFT) . ' | Get My Serv')

@push('styles')
<style>
    .order-detail-section { padding: 60px 0; background-color: var(--bg-color); }
    .detail-card {
        background-color: var(--color-white);
        padding: 40px;
        margin-bottom: 24px;
        border-radius: 8px;
    }
    .detail-card h3 {
        font-family: var(--font-secondary);
        font-size: var(--fs-18);
        font-weight: var(--fw-700);
        color: var(--color-dk-gray);
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid var(--border-color);
    }
    .info-row {
        display: flex;
        justify-content: space-between;
        font-family: var(--font-secondary);
        font-size: var(--fs-15);
        color: var(--color-dk-gray);
        padding: 8px 0;
        border-bottom: 1px solid var(--border-color);
    }
    .info-row:last-child { border-bottom: none; }
    .info-label { opacity: .65; }
    .info-value { font-weight: 600; }
    .table tr th {
        background-color: var(--color-green);
        font-family: var(--font-secondary);
        font-size: var(--fs-14);
        font-weight: var(--fw-600);
        color: var(--color-white);
    }
    .table tr td {
        font-family: var(--font-secondary);
        font-size: var(--fs-14);
        vertical-align: middle;
        border-bottom: 1px solid var(--border-color) !important;
    }
    .status-badge {
        display: inline-block;
        padding: 4px 14px;
        border-radius: 20px;
        font-size: .82rem;
        font-weight: 600;
        font-family: var(--font-secondary);
    }
    .status-pending    { background:#fff3cd; color:#856404; }
    .status-paid       { background:#d1e7dd; color:#0f5132; }
    .status-processing { background:#cff4fc; color:#055160; }
    .status-completed  { background:#d1fae5; color:#065f46; }
    .status-cancelled        { background:#f8d7da; color:#842029; }
    .status-failed           { background:#f8d7da; color:#842029; }
    .status-cancel_requested { background:#ffedd5; color:#c2410c; }
    .refund-requested  { background:#fff3cd; color:#856404; }
    .refund-partial    { background:#cff4fc; color:#055160; }
    .refund-full       { background:#f8d7da; color:#842029; }
    .combo-group {
        border: 2px solid var(--color-green);
        border-radius: 8px;
        margin-bottom: 16px;
        overflow: hidden;
    }
    .combo-header {
        background: linear-gradient(90deg, var(--color-green), #3a7bd5);
        color: #fff;
        padding: 8px 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-family: var(--font-secondary);
        font-size: .88rem;
        font-weight: 600;
    }
    .combo-header .combo-price { opacity: .9; font-size: .82rem; }
    .combo-item-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 16px;
        border-bottom: 1px solid var(--border-color);
        font-family: var(--font-secondary);
        font-size: .9rem;
    }
    .combo-item-row:last-child { border-bottom: none; }
    .combo-item-row.cancelled-item { opacity: .55; }
    .item-name { font-weight: 600; color: var(--color-dk-gray); }
    .item-pkg  { font-size: .78rem; background: var(--font-shade); padding: 2px 8px; border-radius: 4px; margin-left: 6px; }
    .item-price { font-weight: 600; color: var(--color-dk-gray); white-space: nowrap; }
    .item-price.striked { text-decoration: line-through; opacity: .5; }
    .single-item-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid var(--border-color);
        font-family: var(--font-secondary);
        font-size: .9rem;
    }
    .single-item-row:last-child { border-bottom: none; }
    .single-item-row.cancelled-item { opacity: .55; }
    .offer-discount-row {
        display: flex;
        justify-content: space-between;
        font-family: var(--font-secondary);
        font-size: .88rem;
        color: var(--color-green);
        font-weight: 600;
        padding: 6px 0;
        border-bottom: 1px solid var(--border-color);
    }
    .order-total-row {
        display: flex;
        justify-content: space-between;
        font-family: var(--font-secondary);
        font-weight: 700;
        font-size: 1rem;
        padding: 10px 0 0;
    }
    .btn-cancel-item {
        background: none;
        border: 1px solid #dc3545;
        color: #dc3545;
        font-size: .78rem;
        padding: 3px 10px;
        border-radius: 4px;
        cursor: pointer;
        font-family: var(--font-secondary);
        transition: all .2s;
    }
    .btn-cancel-item:hover { background: #dc3545; color: #fff; }
    .badge-cancelled {
        background: #f8d7da;
        color: #842029;
        font-size: .72rem;
        padding: 2px 8px;
        border-radius: 4px;
        font-weight: 600;
    }
    .payment-row {
        display: flex; align-items: center; gap: 16px;
        padding: 14px 0; border-bottom: 1px solid var(--border-color);
    }
    .payment-row:last-child { border-bottom: none; }
    .payment-icon { font-size: 1.6rem; }
    .payment-info { flex: 1; }
    .payment-info .title { font-weight: 600; font-size: .95rem; }
    .payment-info .meta  { font-size: .8rem; opacity: .6; margin-top: 2px; }
    .payment-amount { font-weight: 700; font-family: var(--font-secondary); }
    .btn-back {
        display: inline-block;
        background: none;
        border: 2px solid var(--color-green);
        color: var(--color-green);
        padding: 10px 24px;
        border-radius: 6px;
        font-family: var(--font-secondary);
        font-size: .9rem;
        font-weight: 600;
        text-decoration: none;
        transition: all .2s;
    }
    .btn-back:hover { background: var(--color-green); color: var(--color-white); }
    .btn-refund-request {
        display: inline-block;
        background-color: #dc3545;
        color: var(--color-white);
        padding: 10px 24px;
        border-radius: 6px;
        font-family: var(--font-secondary);
        font-size: .9rem;
        font-weight: 600;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: opacity .2s;
        width: 100%;
        text-align: center;
    }
    .btn-refund-request:hover { opacity: .85; }
</style>
@endpush

@section('content')

    <section class="about-banner ban-faq">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="about-banner-content">
                        <h2>Order #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</h2>
                        <h4>
                            <a href="{{ url('/') }}">home</a>
                            <span>// <a href="{{ route('orders.index') }}" style="color:inherit;">My Orders</a></span>
                            <span>// Order Detail</span>
                        </h4>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="order-detail-section">
        <div class="container">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4">
                    <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div id="cancel-alert" class="alert alert-success alert-dismissible fade mb-4" role="alert" style="display:none!important;">
                <span id="cancel-alert-msg"></span>
                <button type="button" class="btn-close" onclick="this.parentElement.style.display='none'"></button>
            </div>

            <div class="row">

                {{-- Left --}}
                <div class="col-lg-8">

                    {{-- Order Summary --}}
                    <div class="detail-card">
                        <h3><i class="fa-solid fa-receipt me-2" style="color:var(--color-green);"></i>Order Summary</h3>
                        <div class="info-row">
                            <span class="info-label">Order Number</span>
                            <span class="info-value">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Date</span>
                            <span class="info-value">{{ $order->created_at->format('d M Y, h:i A') }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Status</span>
                            <span class="status-badge status-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
                        </div>
                        @if($order->refund_status !== 'none')
                        <div class="info-row">
                            <span class="info-label">Refund Status</span>
                            <span class="status-badge refund-{{ $order->refund_status }}">{{ ucfirst($order->refund_status) }}</span>
                        </div>
                        @endif
                        <div class="info-row">
                            <span class="info-label">Items Total</span>
                            <span class="info-value">AED {{ number_format($order->amount + $order->discount_amount, 2) }}</span>
                        </div>
                        @if($order->discount_amount > 0)
                        <div class="info-row" style="color:var(--color-green);">
                            <span class="info-label" style="opacity:1;">Offer Discount</span>
                            <span class="info-value" style="color:var(--color-green);">− AED {{ number_format($order->discount_amount, 2) }}</span>
                        </div>
                        @endif
                        @if($order->vat_amount > 0)
                        <div class="info-row">
                            <span class="info-label">VAT ({{ $order->vat_rate }}%)</span>
                            <span class="info-value">+ AED {{ number_format($order->vat_amount, 2) }}</span>
                        </div>
                        @endif
                        <div class="info-row">
                            <span class="info-label">Total Paid</span>
                            <span class="info-value" style="color:var(--color-green);font-size:1.1rem;">
                                AED {{ number_format($order->amount, 2) }}
                            </span>
                        </div>
                        @if($order->refunded_amount > 0)
                        <div class="info-row">
                            <span class="info-label">Refunded</span>
                            <span class="info-value" style="color:#dc3545;">− AED {{ number_format($order->refunded_amount, 2) }}</span>
                        </div>
                        @endif
                    </div>

                    {{-- Applied Offers --}}
                    @if($order->offer_snapshot)
                    <div class="detail-card">
                        <h3><i class="fa-solid fa-tag me-2" style="color:var(--color-green);"></i>Applied Offers</h3>
                        @foreach($order->offer_snapshot as $offer)
                        <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 16px;background:linear-gradient(90deg,rgba(3,168,78,.08),rgba(58,123,213,.06));border-radius:8px;margin-bottom:10px;border:1px solid rgba(3,168,78,.2);">
                            <div>
                                <div style="font-family:var(--font-secondary);font-weight:700;color:var(--color-dk-gray);">
                                    <i class="fa-solid fa-circle-check me-1" style="color:var(--color-green);"></i>
                                    {{ $offer['title'] }}
                                </div>
                                <div style="font-family:var(--font-secondary);font-size:.82rem;color:#666;margin-top:3px;">
                                    @if(isset($offer['original_price']))
                                        Original: AED {{ number_format($offer['original_price'], 2) }}
                                        &nbsp;→&nbsp; Offer price: AED {{ number_format($offer['offer_price'], 2) }}
                                    @else
                                        Package Price: AED {{ number_format($offer['offer_price'], 2) }}
                                    @endif
                                </div>
                            </div>
                            @if(isset($offer['discount']))
                            <div style="font-family:var(--font-secondary);font-weight:700;color:var(--color-green);white-space:nowrap;padding-left:12px;">
                                − AED {{ number_format($offer['discount'], 2) }}
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @endif

                    {{-- Order Items (grouped) --}}
                    <div class="detail-card">
                        <h3><i class="fa-solid fa-box me-2" style="color:var(--color-green);"></i>Items</h3>

                        @php
                            $orderItems   = $order->orderItems;
                            $comboGroups  = $orderItems->whereNotNull('combo_offer_id')->groupBy('combo_offer_id');
                            $soloItems    = $orderItems->whereNull('combo_offer_id');
                            $canCancel    = $order->status === 'paid';
                        @endphp

                        {{-- Combo groups --}}
                        @foreach($comboGroups as $offerId => $groupItems)
                            @php
                                $first          = $groupItems->first();
                                $allCancelled   = $groupItems->every(fn($i) => $i->status === 'cancelled');
                                $anyCancelled   = $groupItems->contains(fn($i) => $i->status === 'cancelled');
                                $cancelUrl      = route('orders.items.cancel', [$order, $first]);
                                $comboNames     = $groupItems->pluck('service_name')->join(', ');
                            @endphp
                            <div class="combo-group {{ $allCancelled ? 'opacity-50' : '' }}">
                                <div class="combo-header">
                                    <div>
                                        <i class="fa-solid fa-tag me-1"></i>
                                        Combo Offer: {{ $first->combo_offer_title ?? 'Bundle' }}
                                        @if($allCancelled)
                                            <span class="badge-cancelled ms-2">Cancelled</span>
                                        @endif
                                    </div>
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="combo-price">
                                            AED {{ number_format($first->combo_offer_price ?? $groupItems->sum('price'), 2) }}
                                        </span>
                                        @if($canCancel && !$allCancelled)
                                            <button type="button" class="btn-cancel-item"
                                                    style="border-color:#fff;color:#fff;font-size:.75rem;"
                                                    data-cancel-url="{{ $cancelUrl }}"
                                                    data-is-combo="1"
                                                    data-combo-title="{{ $first->combo_offer_title ?? 'this bundle' }}"
                                                    data-combo-names="{{ $comboNames }}"
                                                    data-combo-price="{{ number_format($first->combo_offer_price ?? $groupItems->sum('price'), 2) }}"
                                                    onclick="confirmCancelCombo(this)">
                                                Cancel Combo
                                            </button>
                                        @endif
                                    </div>
                                </div>
                                @foreach($groupItems as $item)
                                <div class="combo-item-row {{ $item->status === 'cancelled' ? 'cancelled-item' : '' }}">
                                    <div>
                                        <span class="item-name">{{ $item->service_name }}</span>
                                        @if($item->package !== 'standard')
                                            <span class="item-pkg">{{ ucfirst($item->package) }}</span>
                                        @endif
                                        @if($item->status === 'cancelled')
                                            <span class="badge-cancelled ms-2">Cancelled</span>
                                        @endif
                                    </div>
                                    <span class="item-price {{ $item->status === 'cancelled' ? 'striked' : '' }}">
                                        @if($item->price > 0)
                                            AED {{ number_format($item->price, 2) }}
                                        @else
                                            —
                                        @endif
                                    </span>
                                </div>
                                @endforeach
                            </div>
                        @endforeach

                        {{-- Solo items --}}
                        @foreach($soloItems as $item)
                        <div class="single-item-row {{ $item->status === 'cancelled' ? 'cancelled-item' : '' }}">
                            <div>
                                <span class="item-name">{{ $item->service_name }}</span>
                                @if($item->package !== 'standard')
                                    <span class="item-pkg">{{ ucfirst($item->package) }}</span>
                                @endif
                                @if($item->status === 'cancelled')
                                    <span class="badge-cancelled ms-2">Cancelled</span>
                                @endif
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="item-price {{ $item->status === 'cancelled' ? 'striked' : '' }}">
                                    @if($item->price > 0)
                                        AED {{ number_format($item->price, 2) }}
                                    @else
                                        —
                                    @endif
                                </span>
                                @if($canCancel && $item->status === 'active')
                                    <button type="button" class="btn-cancel-item"
                                            data-cancel-url="{{ route('orders.items.cancel', [$order, $item]) }}"
                                            data-is-combo="0"
                                            data-item-name="{{ $item->service_name }}"
                                            data-item-price="{{ number_format($item->price, 2) }}"
                                            onclick="confirmCancelSolo(this)">
                                        Cancel
                                    </button>
                                @endif
                            </div>
                        </div>
                        @endforeach

                        {{-- Fallback for legacy JSON orders with no order_items --}}
                        @if($orderItems->isEmpty())
                            @foreach($order->items ?? [] as $item)
                            <div class="single-item-row">
                                <span class="item-name">{{ $item['service_name'] }}</span>
                                <span class="item-price">AED {{ number_format($item['price'], 2) }}</span>
                            </div>
                            @endforeach
                        @endif

                        {{-- Totals --}}
                        <div class="mt-3 pt-2" style="border-top:2px solid var(--border-color);">
                            @if($order->discount_amount > 0)
                            <div class="offer-discount-row">
                                <span><i class="fa-solid fa-tag me-1"></i>Offer Discount</span>
                                <span>− AED {{ number_format($order->discount_amount, 2) }}</span>
                            </div>
                            @endif
                            @if($order->vat_amount > 0)
                            <div class="offer-discount-row" style="color:var(--color-dk-gray);">
                                <span>VAT ({{ $order->vat_rate }}%)</span>
                                <span>+ AED {{ number_format($order->vat_amount, 2) }}</span>
                            </div>
                            @endif
                            <div class="order-total-row">
                                <span style="font-family:var(--font-secondary);">Total Paid</span>
                                <span style="color:var(--color-green);font-size:1.1rem;">
                                    AED {{ number_format($order->amount, 2) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Payment History --}}
                    @if($order->payments->isNotEmpty())
                    <div class="detail-card">
                        <h3><i class="fa-solid fa-credit-card me-2" style="color:var(--color-green);"></i>Payment History</h3>
                        @foreach($order->payments as $payment)
                        <div class="payment-row">
                            <div class="payment-icon">
                                @if($payment->type === 'charge')
                                    <i class="fa-solid fa-circle-check" style="color:var(--color-green);"></i>
                                @else
                                    <i class="fa-solid fa-rotate-left" style="color:#dc3545;"></i>
                                @endif
                            </div>
                            <div class="payment-info">
                                <div class="title">{{ $payment->getTypeLabel() }}</div>
                                <div class="meta">
                                    {{ $payment->created_at->format('d M Y, h:i A') }} · {{ ucfirst($payment->status) }}
                                </div>
                            </div>
                            <div class="payment-amount"
                                 style="color:{{ $payment->type === 'refund' ? '#dc3545' : 'var(--color-green)' }}">
                                {{ $payment->type === 'refund' ? '−' : '' }}AED {{ number_format($payment->amount, 2) }}
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif

                </div>

                {{-- Right: actions --}}
                <div class="col-lg-4">
                    <div class="detail-card" style="position:sticky;top:100px;">
                        <h3><i class="fa-solid fa-ellipsis me-2" style="color:var(--color-green);"></i>Actions</h3>

                        <a href="{{ route('orders.index') }}" class="btn-back d-block text-center mb-3">
                            <i class="fa fa-arrow-left me-2"></i> Back to Orders
                        </a>

                        @if(in_array($order->status, ['paid', 'processing']))
                        <div style="border-top:1px solid var(--border-color);padding-top:16px;margin-top:4px;">
                            <p style="font-family:var(--font-secondary);font-size:.85rem;color:#666;margin-bottom:12px;">
                                Want to cancel this order? Submit a cancellation request for review.
                            </p>
                            <button type="button" class="btn-refund-request"
                                    style="background-color:#ea580c;"
                                    data-bs-toggle="modal" data-bs-target="#cancelRequestModal">
                                <i class="fa-solid fa-ban me-2"></i> Request Cancellation
                            </button>
                        </div>
                        @elseif($order->status === 'cancel_requested')
                        <div style="border-top:1px solid var(--border-color);padding-top:16px;margin-top:4px;">
                            <div class="alert alert-warning py-2 mb-0" style="font-size:.85rem;">
                                <i class="fa-solid fa-clock me-1"></i>
                                Cancellation request submitted. Our team will review it shortly.
                            </div>
                        </div>
                        @endif


                        <div style="border-top:1px solid var(--border-color);padding-top:16px;margin-top:16px;">
                            <p style="font-family:var(--font-secondary);font-size:.82rem;color:#999;margin:0;">
                                <i class="fa-solid fa-shield-halved me-1"></i>
                                Payments secured by Stripe
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- Cancel Request Modal --}}
    @if(in_array($order->status, ['paid', 'processing']))
    <div class="modal fade" id="cancelRequestModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" style="font-family:var(--font-secondary);">
                        <i class="fa-solid fa-ban me-2 text-warning"></i>Request Order Cancellation
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('orders.cancel-request', $order) }}">
                    @csrf
                    <div class="modal-body">
                        <p style="font-family:var(--font-secondary);font-size:.9rem;color:#444;">
                            You are requesting to cancel Order
                            <strong>#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</strong>
                            (AED {{ number_format($order->amount, 2) }}).
                        </p>
                        <div class="alert alert-warning py-2" style="font-size:.85rem;">
                            <i class="fa-solid fa-circle-info me-1"></i>
                            Your request will be reviewed by our team. The order will only be cancelled once approved.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Keep Order</button>
                        <button type="submit" class="btn btn-warning btn-sm text-white">
                            <i class="fa-solid fa-ban me-1"></i> Submit Cancellation Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- Full Refund Modal --}}
    @if($order->status === 'paid' && $order->refund_status === 'none')
    <div class="modal fade" id="refundModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" style="font-family:var(--font-secondary);">Request a Full Refund</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('orders.refund-request', $order) }}">
                    @csrf
                    <div class="modal-body">
                        <p style="font-family:var(--font-secondary);font-size:.9rem;color:#666;">
                            Order <strong>#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</strong>
                            — AED {{ number_format($order->amount, 2) }}
                        </p>
                        <div class="mb-3">
                            <label class="form-label" style="font-family:var(--font-secondary);font-weight:600;">
                                Reason (optional)
                            </label>
                            <textarea name="reason" class="form-control" rows="3"
                                      placeholder="Let us know why you'd like a refund…"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="fa-solid fa-rotate-left me-1"></i> Submit Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- Combo cancel warning modal --}}
    <div class="modal fade" id="comboCancelModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title" style="font-family:var(--font-secondary);">
                        <i class="fa-solid fa-triangle-exclamation text-warning me-2"></i>
                        Combo Offer — All Items Will Be Cancelled
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p style="font-family:var(--font-secondary);font-size:.9rem;color:#444;" id="combo-warning-text"></p>
                    <div class="alert alert-warning py-2" style="font-size:.85rem;">
                        <i class="fa-solid fa-circle-info me-1"></i>
                        Cancelling any item in a combo offer cancels the entire bundle and triggers a refund request for
                        <strong id="combo-refund-amount"></strong>.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Keep Order</button>
                    <button type="button" class="btn btn-danger btn-sm" id="combo-confirm-btn">
                        <i class="fa-solid fa-rotate-left me-1"></i> Cancel Entire Combo
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Solo cancel confirmation modal --}}
    <div class="modal fade" id="soloCancelModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title" style="font-family:var(--font-secondary);">Cancel Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p style="font-family:var(--font-secondary);font-size:.9rem;color:#444;" id="solo-warning-text"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Keep Item</button>
                    <button type="button" class="btn btn-danger btn-sm" id="solo-confirm-btn">
                        <i class="fa-solid fa-rotate-left me-1"></i> Cancel Item
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
const csrfToken = '{{ csrf_token() }}';
let pendingCancelUrl = null;

function showAlert(msg) {
    const el = document.getElementById('cancel-alert');
    document.getElementById('cancel-alert-msg').textContent = msg;
    el.style.removeProperty('display');
    el.classList.add('show');
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function doCancel(url, onSuccess) {
    fetch(url, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
    })
    .then(r => r.json())
    .then(data => {
        if (data.ok) {
            showAlert(data.message);
            onSuccess && onSuccess();
            setTimeout(() => location.reload(), 2000);
        }
    })
    .catch(() => alert('Something went wrong. Please try again.'));
}

function confirmCancelCombo(btn) {
    const url        = btn.dataset.cancelUrl;
    const title      = btn.dataset.comboTitle;
    const names      = btn.dataset.comboNames;
    const price      = btn.dataset.comboPrice;

    document.getElementById('combo-warning-text').textContent =
        'You are about to cancel the combo "' + title + '" which includes: ' + names + '.';
    document.getElementById('combo-refund-amount').textContent = 'AED ' + price;

    pendingCancelUrl = url;

    document.getElementById('combo-confirm-btn').onclick = function () {
        bootstrap.Modal.getInstance(document.getElementById('comboCancelModal')).hide();
        doCancel(pendingCancelUrl);
    };

    new bootstrap.Modal(document.getElementById('comboCancelModal')).show();
}

function confirmCancelSolo(btn) {
    const url  = btn.dataset.cancelUrl;
    const name  = btn.dataset.itemName;
    const price = btn.dataset.itemPrice;

    document.getElementById('solo-warning-text').textContent =
        'Cancel "' + name + '" (AED ' + price + ')? A refund request will be submitted for review.';

    pendingCancelUrl = url;

    document.getElementById('solo-confirm-btn').onclick = function () {
        bootstrap.Modal.getInstance(document.getElementById('soloCancelModal')).hide();
        doCancel(pendingCancelUrl);
    };

    new bootstrap.Modal(document.getElementById('soloCancelModal')).show();
}
</script>
@endpush
