@extends('provider.layouts.app')
@section('title', 'Order #' . str_pad($order->id, 6, '0', STR_PAD_LEFT))
@section('page-title', 'Order Detail')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">

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
                    <span class="badge {{ $order->getStatusBadgeClass() }} fs-6">
                        {{ ucfirst($order->status) }}
                    </span>
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
                            @if($order->discount_amount > 0)
                                <div class="text-success small">
                                    Discount: − AED {{ number_format($order->discount_amount, 2) }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded">
                            <div class="text-muted small mb-1">Placed On</div>
                            <div class="fw-semibold">{{ $order->created_at->format('d M Y') }}</div>
                            <div class="text-muted small">{{ $order->created_at->format('h:i A') }}</div>
                        </div>
                    </div>
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
                @endphp

                @if($orderItems->isEmpty())
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
                        @php $first = $groupItems->first(); @endphp
                        <div class="mb-3 rounded overflow-hidden" style="border:2px solid #5664d2;">
                            <div class="d-flex justify-content-between align-items-center px-3 py-2"
                                 style="background:linear-gradient(90deg,#5664d2,#4f5fbc);color:#fff;">
                                <div class="fw-semibold small">
                                    <i class="ri-price-tag-3-line me-1"></i>
                                    Combo: {{ $first->combo_offer_title ?? 'Bundle' }}
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
                                <tr><th>Service</th><th>Package</th><th>Price</th><th>Status</th></tr>
                            </thead>
                            <tbody>
                                @foreach($soloItems as $item)
                                <tr class="{{ $item->status === 'cancelled' ? 'text-muted' : '' }}">
                                    <td class="{{ $item->status === 'cancelled' ? 'fw-semibold text-decoration-line-through' : 'fw-semibold' }}">
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

        {{-- Order Files --}}
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
                <div class="text-muted text-center py-3" style="border:2px dashed #e4e6ef;border-radius:10px;">
                    <i class="ri-folder-open-line" style="font-size:2rem;opacity:.3;"></i>
                    <p class="mb-0 mt-1 small">No files uploaded yet.</p>
                </div>
                @else
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
                        </div>
                    </div>
                    <a href="{{ Storage::url($file->file_path) }}" target="_blank"
                       class="btn btn-sm btn-outline-primary flex-shrink-0">
                        <i class="ri-download-line me-1"></i>Download
                    </a>
                </div>
                @endforeach
                @endif
            </div>
        </div>

        <a href="{{ route('provider.orders.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="ri-arrow-left-line me-1"></i>Back to Orders
        </a>

    </div>
</div>
@endsection
