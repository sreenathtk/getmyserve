@extends('provider.layouts.app')
@section('title', 'My Orders')
@section('page-title', 'Assigned Orders')

@section('content')
<div class="card">
    <div class="card-body">
        <h4 class="card-title mb-4">Assigned Orders</h4>

        @if($orders->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="ri-shopping-bag-line" style="font-size:3rem;opacity:.3;"></i>
                <p class="mt-2">No orders have been assigned to you yet.</p>
            </div>
        @else
        <div class="table-responsive">
            <table id="orders-table" class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Items</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
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
                        <td class="text-muted small">{{ $order->orderItems->count() }} item(s)</td>
                        <td>
                            <span class="badge {{ $order->getStatusBadgeClass() }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td class="text-muted small">{{ $order->created_at->format('d M Y') }}</td>
                        <td>
                            <a href="{{ route('provider.orders.show', $order) }}"
                               class="btn btn-sm btn-outline-primary">
                                <i class="ri-eye-line me-1"></i>View
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
$('#orders-table').DataTable({ order: [[5, 'desc']], pageLength: 25 });
</script>
@endsection
