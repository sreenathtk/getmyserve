@extends('admin.layouts.app')
@section('title', 'Payments')
@section('page-title', 'Payments')

@section('content')
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <div class="text-muted small mb-1">Total Charged</div>
                <div class="fw-bold fs-4 text-success">AED {{ number_format($totalCharged, 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <div class="text-muted small mb-1">Total Refunded</div>
                <div class="fw-bold fs-4 text-danger">AED {{ number_format($totalRefunded, 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <div class="text-muted small mb-1">Net Revenue</div>
                <div class="fw-bold fs-4 text-primary">AED {{ number_format($totalCharged - $totalRefunded, 2) }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title mb-0">All Payments</h4>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-primary btn-sm">
                        <i class="ri-shopping-bag-line me-1"></i> Orders
                    </a>
                </div>

                <form method="GET" action="{{ route('admin.payments.index') }}" class="row g-2 align-items-end mb-4">
                    <div class="col-sm-4">
                        <label class="form-label small text-muted mb-1">Search</label>
                        <input type="text" name="search" class="form-control form-control-sm"
                               placeholder="Payment intent, refund ID, customer…"
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-sm-2">
                        <label class="form-label small text-muted mb-1">Type</label>
                        <select name="type" class="form-select form-select-sm">
                            <option value="">All Types</option>
                            <option value="charge" {{ request('type') === 'charge' ? 'selected' : '' }}>Payment</option>
                            <option value="refund" {{ request('type') === 'refund' ? 'selected' : '' }}>Refund</option>
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <label class="form-label small text-muted mb-1">Status</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="">All</option>
                            @foreach(['succeeded','pending','failed','canceled'] as $s)
                                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>
                                    {{ ucfirst($s) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-auto d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="ri-filter-line me-1"></i> Filter
                        </button>
                        @if(request()->hasAny(['search','type','status']))
                            <a href="{{ route('admin.payments.index') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="ri-close-line me-1"></i> Clear
                            </a>
                        @endif
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover table-centered mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Order</th>
                                <th>Customer</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payments as $index => $payment)
                            <tr>
                                <td>{{ $payment->id }}</td>
                                <td>
                                    @if($payment->order_id)
                                        <a href="{{ route('admin.orders.show', $payment->order) }}" class="text-primary fw-semibold">
                                            Order #{{ str_pad($payment->order_id, 5, '0', STR_PAD_LEFT) }}
                                        </a>
                                    @elseif($payment->enquiry_id)
                                        <a href="{{ route('admin.enquiries.show', $payment->enquiry) }}" class="text-primary fw-semibold">
                                            Request #{{ str_pad($payment->enquiry_id, 5, '0', STR_PAD_LEFT) }}
                                        </a>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($payment->order?->user)
                                        <div class="fw-semibold">{{ $payment->order->user->name }}</div>
                                        <div class="text-muted small">{{ $payment->order->user->email }}</div>
                                    @elseif($payment->enquiry)
                                        <div class="fw-semibold">{{ $payment->enquiry->full_name }}</div>
                                        <div class="text-muted small">{{ $payment->enquiry->email }}</div>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($payment->type === 'charge')
                                        <span class="badge badge-soft-success">Payment</span>
                                    @else
                                        <span class="badge badge-soft-warning">Refund</span>
                                    @endif
                                </td>
                                <td class="fw-semibold
                                    {{ $payment->type === 'refund' ? 'text-danger' : 'text-success' }}">
                                    {{ $payment->type === 'refund' ? '−' : '' }}AED {{ number_format($payment->amount, 2) }}
                                </td>
                                <td>
                                    <span class="badge {{ $payment->getStatusBadgeClass() }}">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </td>
                                <td class="text-muted small">{{ $payment->created_at->format('d M Y, h:i A') }}</td>
                                <td>
                                    <a href="{{ route('admin.payments.show', $payment) }}"
                                       class="btn btn-info btn-sm">
                                        <i class="ri-eye-line"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">No payments found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $payments->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
