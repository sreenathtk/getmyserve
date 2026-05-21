@extends('layouts.app')

@section('title', 'My Orders | Get My Serv')

@push('styles')
<style>
    .orders-section {
        padding: 60px 0;
        background-color: var(--bg-color);
    }
    .orders-card {
        background-color: var(--color-white);
        padding: 40px;
    }
    .orders-card h2 {
        font-family: var(--font-secondary);
        font-size: var(--fs-20);
        font-weight: var(--fw-700);
        color: var(--color-dk-gray);
        margin-bottom: 24px;
    }
    .table tr th {
        background-color: var(--color-green);
        font-family: var(--font-secondary);
        font-size: var(--fs-15);
        font-weight: var(--fw-600);
        color: var(--color-white);
    }
    .table tr td {
        font-family: var(--font-secondary);
        font-size: var(--fs-15);
        font-weight: var(--fw-500);
        vertical-align: middle;
        border-bottom: 1px solid var(--border-color) !important;
    }
    .status-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: .82rem;
        font-weight: 600;
        font-family: var(--font-secondary);
    }
    .status-pending    { background:#fff3cd; color:#856404; }
    .status-paid       { background:#d1e7dd; color:#0f5132; }
    .status-processing { background:#cff4fc; color:#055160; }
    .status-completed  { background:#d1fae5; color:#065f46; }
    .status-cancelled  { background:#f8d7da; color:#842029; }
    .status-failed     { background:#f8d7da; color:#842029; }
    .refund-requested  { background:#fff3cd; color:#856404; }
    .refund-partial    { background:#cff4fc; color:#055160; }
    .refund-full       { background:#f8d7da; color:#842029; }
    .btn-view-order {
        background-color: var(--color-green);
        color: var(--color-white);
        padding: 6px 16px;
        border-radius: 5px;
        font-family: var(--font-secondary);
        font-size: .85rem;
        font-weight: 600;
        text-decoration: none;
        display: inline-block;
        transition: opacity .2s;
    }
    .btn-view-order:hover { opacity:.85; color:var(--color-white); }
    .orders-empty {
        text-align: center;
        padding: 60px 20px;
        color: var(--color-dk-gray);
    }
    .orders-empty i { font-size: 4rem; opacity:.2; display:block; margin-bottom:16px; }
</style>
@endpush

@section('content')

    {{-- Banner --}}
    <section class="about-banner ban-faq">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="about-banner-content">
                        <h2>My Orders</h2>
                        <h4>
                            <a href="{{ url('/') }}">home</a>
                            <span>// My Orders</span>
                        </h4>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="orders-section">
        <div class="container">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="orders-card">
                <h2>Order History</h2>

                @if($orders->isEmpty())
                    <div class="orders-empty">
                        <i class="fa-solid fa-box-open"></i>
                        <p style="font-size:1.1rem;margin-bottom:8px;">No orders yet</p>
                        <p style="font-size:.9rem;opacity:.6;margin-bottom:20px;">
                            Once you complete a purchase, your orders will appear here.
                        </p>
                        <a href="{{ route('services.index') }}" class="btn-view-order">
                            Browse Services <i class="fa fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Date</th>
                                    <th>Items</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Refund</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                <tr>
                                    <td><strong>#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</strong></td>
                                    <td>{{ $order->created_at->format('d M Y') }}</td>
                                    <td>
                                        @php
                                            $items = $order->items ?? [];
                                            $count = count($items);
                                        @endphp
                                        {{ $count }} {{ Str::plural('item', $count) }}
                                    </td>
                                    <td><strong>AED {{ number_format($order->amount, 2) }}</strong></td>
                                    <td>
                                        <span class="status-badge status-{{ $order->status }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($order->refund_status !== 'none')
                                            <span class="status-badge refund-{{ $order->refund_status }}">
                                                {{ ucfirst($order->refund_status) }}
                                            </span>
                                        @else
                                            <span style="opacity:.4;">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('orders.show', $order) }}" class="btn-view-order">
                                            View
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $orders->links() }}
                    </div>
                @endif
            </div>

        </div>
    </section>

@endsection
