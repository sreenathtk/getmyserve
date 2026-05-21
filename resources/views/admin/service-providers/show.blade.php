@extends('admin.layouts.app')
@section('title', $serviceProvider->company_name)
@section('page-title', 'Service Provider Detail')

@section('content')

{{-- Provider Summary --}}
<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                @if($serviceProvider->company_logo)
                    <img src="{{ asset('storage/' . $serviceProvider->company_logo) }}"
                         alt="Logo" class="rounded" style="width:56px;height:56px;object-fit:cover;">
                @else
                    <div class="rounded bg-light d-flex align-items-center justify-content-center"
                         style="width:56px;height:56px;font-size:1.4rem;color:#adb5bd;">
                        <i class="ri-building-2-line"></i>
                    </div>
                @endif
                <div>
                    <h5 class="mb-0 fw-semibold">{{ $serviceProvider->company_name }}</h5>
                    <div class="text-muted small">{{ $serviceProvider->user->email ?? '—' }}</div>
                    <div class="mt-1 d-flex gap-2 flex-wrap">
                        @if($serviceProvider->approval_status === 'approved')
                            <span class="badge badge-soft-success">Approved</span>
                        @elseif($serviceProvider->approval_status === 'rejected')
                            <span class="badge badge-soft-danger">Rejected</span>
                        @else
                            <span class="badge badge-soft-warning">Pending Approval</span>
                        @endif
                        @if($serviceProvider->is_active)
                            <span class="badge badge-soft-success">Active</span>
                        @else
                            <span class="badge badge-soft-danger">Disabled</span>
                        @endif
                        @if($serviceProvider->is_trusted)
                            <span class="badge badge-soft-info">Trusted</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <form method="POST"
                      action="{{ route('admin.service-providers.toggle-trusted', $serviceProvider) }}"
                      class="m-0">
                    @csrf
                    @method('PATCH')
                    <div class="form-check form-switch mb-0 d-flex align-items-center gap-2">
                        <input class="form-check-input" type="checkbox" role="switch"
                               id="trustedToggle"
                               onchange="this.form.submit()"
                               {{ $serviceProvider->is_trusted ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold small text-nowrap" for="trustedToggle">
                            Trusted Provider
                        </label>
                    </div>
                </form>
                <a href="{{ route('admin.service-providers.edit', $serviceProvider) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="ri-edit-line me-1"></i> Edit
                </a>
                <a href="{{ route('admin.service-providers.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="ri-arrow-left-line me-1"></i> Back
                </a>
            </div>
        </div>

        <hr class="my-3">

        <div class="row g-3">
            <div class="col-sm-6 col-lg-3">
                <div class="text-muted small mb-1">Primary Contact</div>
                <div class="fw-semibold">{{ $serviceProvider->primary_contact_name ?: '—' }}</div>
                <div class="text-muted small">{{ $serviceProvider->primary_contact_mobile }}</div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="text-muted small mb-1">Emirates / City</div>
                <div>{{ $serviceProvider->emirate_city ?: '—' }}</div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="text-muted small mb-1">Trade License</div>
                <div>{{ $serviceProvider->trade_license ?: '—' }}</div>
                @if($serviceProvider->license_expiry_date)
                    <div class="text-muted small">Expires {{ \Carbon\Carbon::parse($serviceProvider->license_expiry_date)->format('d M Y') }}</div>
                @endif
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="text-muted small mb-1">Services</div>
                <div class="d-flex flex-wrap gap-1">
                    @forelse($serviceProvider->services as $svc)
                        <span class="badge badge-soft-primary">{{ $svc->name }}</span>
                    @empty
                        <span class="text-muted small">None</span>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tabs --}}
<ul class="nav nav-tabs mb-0" id="spTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="enq-tab" data-bs-toggle="tab" data-bs-target="#enq-panel"
                type="button" role="tab">
            <i class="ri-questionnaire-line me-1"></i>
            Service Requests
            <span class="badge badge-soft-secondary ms-1">{{ $enquiries->total() }}</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="ord-tab" data-bs-toggle="tab" data-bs-target="#ord-panel"
                type="button" role="tab">
            <i class="ri-shopping-bag-line me-1"></i>
            Orders
            <span class="badge badge-soft-secondary ms-1">{{ $orders->total() }}</span>
        </button>
    </li>
</ul>

<div class="tab-content">

    {{-- ── Service Requests ── --}}
    <div class="tab-pane fade show active" id="enq-panel" role="tabpanel">
        <div class="card border-top-0 rounded-top-0">
            <div class="card-body">

                <form method="GET" action="{{ route('admin.service-providers.show', $serviceProvider) }}"
                      class="row g-2 align-items-end mb-3">
                    {{-- preserve orders tab filters --}}
                    @if(request('ord_search')) <input type="hidden" name="ord_search" value="{{ request('ord_search') }}"> @endif
                    @if(request('ord_status')) <input type="hidden" name="ord_status" value="{{ request('ord_status') }}"> @endif

                    <div class="col-sm-4">
                        <label class="form-label small text-muted mb-1">Search</label>
                        <input type="text" name="enq_search" class="form-control form-control-sm"
                               placeholder="Name, email or phone…" value="{{ request('enq_search') }}">
                    </div>
                    <div class="col-sm-3">
                        <label class="form-label small text-muted mb-1">Status</label>
                        <select name="enq_status" class="form-select form-select-sm">
                            <option value="">All Statuses</option>
                            @foreach(['pending' => 'Pending', 'in_progress' => 'In Progress', 'under_processing' => 'Under Processing', 'completed' => 'Completed', 'resolved' => 'Resolved'] as $val => $label)
                                <option value="{{ $val }}" {{ request('enq_status') === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-auto d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="ri-filter-line me-1"></i> Filter
                        </button>
                        @if(request()->hasAny(['enq_search', 'enq_status']))
                            <a href="{{ route('admin.service-providers.show', $serviceProvider) }}" class="btn btn-sm btn-outline-secondary">
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
                                <th>Customer</th>
                                <th>Service</th>
                                <th>Status</th>
                                <th>Submitted</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($enquiries as $index => $enq)
                            <tr>
                                <td>{{ $enquiries->firstItem() + $index }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $enq->full_name }}</div>
                                    <div class="text-muted small">{{ $enq->email }}</div>
                                    <div class="text-muted small">{{ $enq->phone }}</div>
                                </td>
                                <td>{{ $enq->service->name ?? '—' }}</td>
                                <td>
                                    @php
                                        $enqBadge = match($enq->status) {
                                            'pending'          => 'badge bg-warning-subtle text-warning-emphasis',
                                            'in_progress'      => 'badge bg-info-subtle text-info-emphasis',
                                            'under_processing' => 'badge bg-primary-subtle text-primary-emphasis',
                                            'completed'        => 'badge badge-soft-teal',
                                            'resolved'         => 'badge bg-success-subtle text-success-emphasis',
                                            default            => 'badge badge-soft-secondary',
                                        };
                                        $enqLabel = match($enq->status) {
                                            'in_progress'      => 'In Progress',
                                            'under_processing' => 'Under Processing',
                                            default            => ucfirst($enq->status),
                                        };
                                    @endphp
                                    <span class="{{ $enqBadge }}">{{ $enqLabel }}</span>
                                </td>
                                <td class="text-muted small">{{ $enq->created_at->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.enquiries.show', $enq) }}"
                                       class="btn btn-sm btn-outline-primary" title="View">
                                        <i class="ri-eye-line"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No service requests found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($enquiries->hasPages())
                <div class="mt-3">{{ $enquiries->links() }}</div>
                @endif

            </div>
        </div>
    </div>

    {{-- ── Orders ── --}}
    <div class="tab-pane fade" id="ord-panel" role="tabpanel">
        <div class="card border-top-0 rounded-top-0">
            <div class="card-body">

                <form method="GET" action="{{ route('admin.service-providers.show', $serviceProvider) }}"
                      class="row g-2 align-items-end mb-3">
                    {{-- preserve enquiries tab filters --}}
                    @if(request('enq_search')) <input type="hidden" name="enq_search" value="{{ request('enq_search') }}"> @endif
                    @if(request('enq_status')) <input type="hidden" name="enq_status" value="{{ request('enq_status') }}"> @endif

                    <div class="col-sm-4">
                        <label class="form-label small text-muted mb-1">Search</label>
                        <input type="text" name="ord_search" class="form-control form-control-sm"
                               placeholder="Order # or customer…" value="{{ request('ord_search') }}">
                    </div>
                    <div class="col-sm-3">
                        <label class="form-label small text-muted mb-1">Status</label>
                        <select name="ord_status" class="form-select form-select-sm">
                            <option value="">All Statuses</option>
                            @foreach(['pending','paid','processing','completed','cancelled','failed'] as $s)
                                <option value="{{ $s }}" {{ request('ord_status') === $s ? 'selected' : '' }}>
                                    {{ ucfirst($s) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-auto d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-primary" name="_tab" value="ord">
                            <i class="ri-filter-line me-1"></i> Filter
                        </button>
                        @if(request()->hasAny(['ord_search', 'ord_status']))
                            <a href="{{ route('admin.service-providers.show', $serviceProvider) }}"
                               class="btn btn-sm btn-outline-secondary">
                                <i class="ri-close-line me-1"></i> Clear
                            </a>
                        @endif
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover table-centered mb-0">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Refund</th>
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
                                <td class="text-muted small">{{ $order->created_at->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.orders.show', $order) }}"
                                       class="btn btn-sm btn-outline-primary" title="View">
                                        <i class="ri-eye-line"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">No orders found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($orders->hasPages())
                <div class="mt-3">{{ $orders->links() }}</div>
                @endif

            </div>
        </div>
    </div>

</div>

@endsection

@section('scripts')
<script>
    // Re-activate the Orders tab when an order filter was submitted
    document.addEventListener('DOMContentLoaded', function () {
        const params = new URLSearchParams(window.location.search);
        if (params.has('ord_search') || params.has('ord_status') || params.get('_tab') === 'ord') {
            const ordTab = document.getElementById('ord-tab');
            if (ordTab) {
                bootstrap.Tab.getOrCreateInstance(ordTab).show();
            }
        }
    });
</script>
@endsection
