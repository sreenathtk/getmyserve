@extends('admin.layouts.app')
@section('title', 'Add Staff')
@section('page-title', 'Add Staff Member')

@section('content')
<div class="row">
    <div class="col-12">
        <form action="{{ route('admin.staff.store') }}" method="POST">
            @csrf

            {{-- Account Details --}}
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-4">Account Details</h5>

                    <div class="row mb-3">
                        <label for="name" class="col-sm-2 col-form-label">Full Name <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                id="name" name="name" value="{{ old('name') }}" placeholder="e.g. John Smith" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="email" class="col-sm-2 col-form-label">Email <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                id="email" name="email" value="{{ old('email') }}" placeholder="staff@example.com" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row mb-0">
                        <label for="password" class="col-sm-2 col-form-label">Password <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                id="password" name="password" placeholder="Min. 8 characters" required>
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Category & Service Assignments --}}
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <h5 class="card-title mb-0">Category &amp; Service Assignments</h5>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="select-all-btn">Select All Categories</button>
                    </div>
                    <p class="text-muted small mb-4">Select which categories this staff member can work on, then choose specific services or grant access to all services within a category.</p>

                    @if($categories->isEmpty())
                        <p class="text-muted fst-italic">No active categories available.</p>
                    @else
                        <div class="row g-3">
                            @foreach($categories as $category)
                            @php
                                $categoryServices = $category->subCategories->flatMap->services;
                            @endphp
                            <div class="col-md-6 col-xl-4">
                                <div class="card h-100 border category-card" data-category="{{ $category->id }}">
                                    <div class="card-header bg-light d-flex align-items-center gap-2 py-2">
                                        <input class="form-check-input category-checkbox flex-shrink-0 mt-0"
                                            type="checkbox" name="categories[]" id="cat_{{ $category->id }}"
                                            value="{{ $category->id }}"
                                            {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold mb-0 flex-grow-1" for="cat_{{ $category->id }}">
                                            {{ $category->name }}
                                        </label>
                                        <span class="badge bg-secondary-subtle text-secondary-emphasis">
                                            {{ $categoryServices->count() }} {{ Str::plural('service', $categoryServices->count()) }}
                                        </span>
                                    </div>
                                    <div class="card-body category-body py-2" style="{{ in_array($category->id, old('categories', [])) ? '' : 'display:none;' }}">
                                        @if($categoryServices->isEmpty())
                                            <p class="text-muted small fst-italic mb-0">No active services in this category.</p>
                                        @else
                                            <div class="mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input all-services-check"
                                                        type="checkbox" name="all_services[]"
                                                        id="all_{{ $category->id }}"
                                                        value="{{ $category->id }}"
                                                        {{ in_array($category->id, old('all_services', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label fw-semibold text-primary" for="all_{{ $category->id }}">
                                                        All Services
                                                    </label>
                                                </div>
                                            </div>
                                            <hr class="my-2">
                                            <div class="service-list" style="{{ in_array($category->id, old('all_services', [])) ? 'opacity:0.4;pointer-events:none;' : '' }}">
                                                @foreach($categoryServices as $service)
                                                <div class="form-check">
                                                    <input class="form-check-input service-check"
                                                        type="checkbox"
                                                        name="services[{{ $category->id }}][]"
                                                        id="svc_{{ $service->id }}"
                                                        value="{{ $service->id }}"
                                                        {{ in_array($service->id, old('services.' . $category->id, [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label small" for="svc_{{ $service->id }}">
                                                        {{ $service->name }}
                                                    </label>
                                                </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="ri-save-line me-1"></i> Save Staff Member
                </button>
                <a href="{{ route('admin.staff.index') }}" class="btn btn-secondary">Cancel</a>
            </div>

        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    // Toggle category body when category checkbox changes
    document.querySelectorAll('.category-checkbox').forEach(function (chk) {
        chk.addEventListener('change', function () {
            const card = this.closest('.category-card');
            const body = card.querySelector('.category-body');
            body.style.display = this.checked ? '' : 'none';
            if (!this.checked) {
                // Clear all selections inside
                card.querySelectorAll('input[type=checkbox]').forEach(c => { if (c !== this) c.checked = false; });
                const serviceList = card.querySelector('.service-list');
                if (serviceList) { serviceList.style.opacity = ''; serviceList.style.pointerEvents = ''; }
            }
        });
    });

    // Toggle "All Services" disables individual service checkboxes
    document.querySelectorAll('.all-services-check').forEach(function (chk) {
        chk.addEventListener('change', function () {
            const serviceList = this.closest('.card-body').querySelector('.service-list');
            if (!serviceList) return;
            if (this.checked) {
                serviceList.style.opacity = '0.4';
                serviceList.style.pointerEvents = 'none';
                serviceList.querySelectorAll('input[type=checkbox]').forEach(c => c.checked = false);
            } else {
                serviceList.style.opacity = '';
                serviceList.style.pointerEvents = '';
            }
        });
    });

    // Select all categories button
    document.getElementById('select-all-btn').addEventListener('click', function () {
        const allChecked = [...document.querySelectorAll('.category-checkbox')].every(c => c.checked);
        document.querySelectorAll('.category-checkbox').forEach(function (chk) {
            chk.checked = !allChecked;
            chk.dispatchEvent(new Event('change'));
        });
        this.textContent = allChecked ? 'Select All Categories' : 'Deselect All Categories';
    });
})();
</script>
@endsection
