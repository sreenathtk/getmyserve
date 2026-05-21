@extends('admin.layouts.app')
@section('title', 'Orders')
@section('page-title', 'Orders Management')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title mb-0">All Orders</h4>
                    <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-primary btn-sm">
                        <i class="ri-bank-card-line me-1"></i> Payments
                    </a>
                </div>

                {{-- Filters --}}
                <form method="GET" action="{{ route('admin.orders.index') }}" class="row g-2 align-items-end mb-4">
                    <div class="col-sm-3">
                        <label class="form-label small text-muted mb-1">Search</label>
                        <input type="text" name="search" class="form-control form-control-sm"
                               placeholder="Order # or customer…" value="{{ request('search') }}">
                    </div>
                    <div class="col-sm-2">
                        <label class="form-label small text-muted mb-1">Status</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="">All Statuses</option>
                            @foreach(['pending','paid','processing','completed','cancelled','failed'] as $s)
                                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>
                                    {{ ucfirst($s) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <label class="form-label small text-muted mb-1">Refund Status</label>
                        <select name="refund_status" class="form-select form-select-sm">
                            <option value="">All</option>
                            @foreach(['none','requested','partial','full'] as $s)
                                <option value="{{ $s }}" {{ request('refund_status') === $s ? 'selected' : '' }}>
                                    {{ ucfirst($s) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-auto d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="ri-filter-line me-1"></i> Filter
                        </button>
                        @if(request()->hasAny(['search','status','refund_status']))
                            <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="ri-close-line me-1"></i> Clear
                            </a>
                        @endif
                    </div>
                </form>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show py-2">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover table-centered mb-0" id="orders-table">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Refund</th>
                                <th>Assigned Staff</th>
                                <th>Service Provider</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                            <tr>
                                <td><strong>#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</strong></td>
                                <td>
                                    @if($order->user)
                                        <div class="fw-semibold">{{ $order->user->name }}</div>
                                        <div class="text-muted small">{{ $order->user->email }}</div>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="fw-semibold">AED {{ number_format($order->amount, 2) }}</td>
                                <td>
                                    <span class="badge {{ $order->getStatusBadgeClass() }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if($order->refund_status !== 'none')
                                        <span class="badge {{ $order->getRefundStatusBadgeClass() }}">
                                            {{ ucfirst($order->refund_status) }}
                                        </span>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $order->assignedStaff?->name ?? '—' }}
                                </td>
                                <td>
                                    {{ $order->assignedSp?->name ?? '—' }}
                                </td>
                                <td class="text-muted small">{{ $order->created_at->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-info btn-sm">
                                        <i class="ri-eye-line"></i> View
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">No orders found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@if($orders->isNotEmpty())
@section('scripts')
<script>
    $(document).ready(function() {
        $('#orders-table').DataTable({
            paging: false,
            searching: false,
            ordering: true,
            info: false,
        });
    });
</script>
@endsection
@endif
@endsection
