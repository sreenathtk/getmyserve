@extends('admin.layouts.app')
@section('title', 'Services')
@section('page-title', 'Services Management')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title mb-0">All Services</h4>
                    <a href="{{ route('admin.services.create') }}" class="btn btn-primary btn-sm">
                        <i class="ri-add-line me-1"></i> Add Service
                    </a>
                </div>

                {{-- Filter bar --}}
                <form method="GET" action="{{ route('admin.services.index') }}" class="row g-2 align-items-end mb-4">
                    <div class="col-sm-4">
                        <label class="form-label small text-muted mb-1">Category</label>
                        <select class="form-select form-select-sm" name="category" id="filter_category">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}"
                                    {{ request('category') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <label class="form-label small text-muted mb-1">Sub-category</label>
                        <select class="form-select form-select-sm" name="sub_category" id="filter_sub_category">
                            <option value="">All Sub-categories</option>
                            @foreach($subCategories as $sc)
                                <option value="{{ $sc->id }}"
                                    {{ request('sub_category') == $sc->id ? 'selected' : '' }}>
                                    {{ $sc->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-auto d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="ri-filter-line me-1"></i> Filter
                        </button>
                        @if(request()->hasAny(['category', 'sub_category']))
                            <a href="{{ route('admin.services.index') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="ri-close-line me-1"></i> Clear
                            </a>
                        @endif
                    </div>
                </form>

                @if(request()->hasAny(['category', 'sub_category']))
                    <p class="text-muted small mb-3">
                        Showing <strong>{{ $services->count() }}</strong> {{ Str::plural('service', $services->count()) }}
                        @if(request('sub_category'))
                            in sub-category <strong>{{ $subCategories->firstWhere('id', request('sub_category'))?->name }}</strong>
                        @elseif(request('category'))
                            in category <strong>{{ $categories->firstWhere('id', request('category'))?->name }}</strong>
                        @endif
                    </p>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover table-centered mb-0" id="services-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Service Name</th>
                                <th>Category</th>
                                <th>Sub-category</th>
                                <th>Service Type</th>
                                <th>Rating</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($services as $index => $service)
                            @php
                                $avg = $service->active_reviews_avg_rating
                                    ? number_format($service->active_reviews_avg_rating, 1)
                                    : null;
                                $reviewCount = $service->active_reviews_count ?? 0;
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><strong>{{ $service->name }}</strong></td>
                                <td>
                                    @if($service->subCategory)
                                        <span class="text-muted">{{ $service->subCategory->category->name }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($service->subCategory)
                                        {{ $service->subCategory->name }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($service->service_type === 'book_now')
                                        <span class="badge badge-soft-primary">Book Now</span>
                                    @elseif($service->service_type === 'enquire_now')
                                        <span class="badge badge-soft-warning">Enquire Now</span>
                                    @else
                                        <span class="badge badge-soft-info">Book & Enquire</span>
                                    @endif
                                </td>
                                <td>
                                    @if($avg !== null)
                                        <div class="d-flex align-items-center gap-1">
                                            <i class="ri-star-fill text-warning"></i>
                                            <span class="fw-semibold">{{ $avg }}</span>
                                            <span class="text-muted small">({{ $reviewCount }})</span>
                                        </div>
                                    @else
                                        <span class="text-muted small fst-italic">No reviews</span>
                                    @endif
                                </td>
                                <td>
                                    @if($service->is_active)
                                        <span class="badge badge-soft-success">Active</span>
                                    @else
                                        <span class="badge badge-soft-danger">Disabled</span>
                                    @endif
                                </td>
                                <td class="text-nowrap">
                                    <a href="{{ route('admin.services.reviews.index', $service) }}" class="btn btn-secondary btn-sm" title="Reviews">
                                        <i class="ri-chat-3-line"></i>
                                    </a>
                                    <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-info btn-sm" title="Edit">
                                        <i class="ri-edit-line"></i>
                                    </a>
                                    <form action="{{ route('admin.services.toggle', $service) }}" method="POST" class="d-inline">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-{{ $service->is_active ? 'warning' : 'success' }} btn-sm">
                                            <i class="ri-{{ $service->is_active ? 'close' : 'check' }}-line"></i>
                                            {{ $service->is_active ? 'Disable' : 'Enable' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">No services found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    $(document).ready(function() {
        $('#services-table').DataTable({
            responsive: true,
            paging: true,
            searching: true,
            ordering: true
        });
    });

    const subCategoryUrl = (categoryId) =>
        `{{ url('admin/categories') }}/${categoryId}/sub-categories-list`;

    document.getElementById('filter_category').addEventListener('change', function () {
        const catId    = this.value;
        const subSel   = document.getElementById('filter_sub_category');
        subSel.innerHTML = '<option value="">All Sub-categories</option>';

        if (!catId) return;

        fetch(subCategoryUrl(catId))
            .then(r => r.json())
            .then(data => {
                data.forEach(sc => {
                    const opt = document.createElement('option');
                    opt.value = sc.id;
                    opt.textContent = sc.name;
                    subSel.appendChild(opt);
                });
            });
    });
</script>
@endsection
@endsection
