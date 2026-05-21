@extends('admin.layouts.app')
@section('title', 'Customers')
@section('page-title', 'Customers')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title mb-0">Customers</h4>
                    <a href="{{ route('admin.customers.create') }}" class="btn btn-primary btn-sm">
                        <i class="ri-user-add-line me-1"></i> Add Customer
                    </a>
                </div>

                {{-- Filters --}}
                <form method="GET" action="{{ route('admin.customers.index') }}" class="row g-2 align-items-end mb-4">
                    <div class="col-sm-4">
                        <label class="form-label small text-muted mb-1">Search</label>
                        <input type="text" class="form-control form-control-sm" name="search"
                            placeholder="Name, email, phone, city…" value="{{ request('search') }}">
                    </div>
                    <div class="col-sm-3">
                        <label class="form-label small text-muted mb-1">Country</label>
                        <select class="form-select form-select-sm" name="country">
                            <option value="">All Countries</option>
                            @foreach($countries as $code => $name)
                                <option value="{{ $code }}" {{ request('country') === $code ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <label class="form-label small text-muted mb-1">Status</label>
                        <select class="form-select form-select-sm" name="status">
                            <option value="">All</option>
                            <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-sm-auto d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="ri-filter-line me-1"></i> Filter
                        </button>
                        @if(request()->hasAny(['search','country','status']))
                            <a href="{{ route('admin.customers.index') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="ri-close-line me-1"></i> Clear
                            </a>
                        @endif
                    </div>
                </form>

                <table id="customers-table" class="table table-hover table-bordered dt-responsive nowrap w-100 align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Country</th>
                            <th>City</th>
                            <th>Reviews</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $i => $customer)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td class="fw-semibold">{{ $customer->name }}</td>
                            <td>{{ $customer->email }}</td>
                            <td>{{ $customer->phone ?? '—' }}</td>
                            <td>
                                @if($customer->country)
                                    {{ $countries[$customer->country] ?? $customer->country }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>{{ $customer->city ?? '—' }}</td>
                            <td>
                                @if($customer->reviews_count > 0)
                                    <span class="badge bg-info-subtle text-info-emphasis">
                                        {{ $customer->reviews_count }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($customer->is_active)
                                    <span class="badge bg-success-subtle text-success-emphasis">Active</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger-emphasis">Inactive</span>
                                @endif
                            </td>
                            <td class="small text-muted">{{ $customer->created_at->format('d M Y') }}</td>
                            <td class="text-nowrap">
                                <a href="{{ route('admin.customers.show', $customer) }}"
                                   class="btn btn-sm btn-outline-secondary me-1" title="View">
                                    <i class="ri-eye-line"></i>
                                </a>
                                <a href="{{ route('admin.customers.edit', $customer) }}"
                                   class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                    <i class="ri-edit-line"></i>
                                </a>
                                <form action="{{ route('admin.customers.toggle', $customer) }}" method="POST" class="d-inline">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                        class="btn btn-sm {{ $customer->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                        title="{{ $customer->is_active ? 'Disable' : 'Enable' }}"
                                        onclick="return confirm('{{ $customer->is_active ? 'Disable' : 'Enable' }} this customer?')">
                                        <i class="ri-{{ $customer->is_active ? 'forbid' : 'check' }}-line"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">No customers found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $('#customers-table').DataTable({
        responsive: true,
        pageLength: 25,
        columnDefs: [{ orderable: false, targets: [6, 9] }]
    });
</script>
@endsection
