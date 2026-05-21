@extends('admin.layouts.app')
@section('title', 'Sub-categories — ' . $category->name)
@section('page-title', $category->name . ' — Sub-categories')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Categories</a></li>
                <li class="breadcrumb-item active">{{ $category->name }}</li>
            </ol>
        </nav>

        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="card-title mb-1">Sub-categories of <strong>{{ $category->name }}</strong></h4>
                        <p class="text-muted mb-0 small">{{ $category->description }}</p>
                    </div>
                    <a href="{{ route('admin.categories.sub-categories.create', $category) }}" class="btn btn-primary btn-sm">
                        <i class="ri-add-line me-1"></i> Add Sub-category
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-centered mb-0" id="sub-categories-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Sub-category Name</th>
                                <th>Description</th>
                                <th>Services</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($subCategories as $index => $subCategory)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><strong>{{ $subCategory->name }}</strong></td>
                                <td>{{ Str::limit($subCategory->description, 50) }}</td>
                                <td>
                                    <a href="{{ route('admin.services.index') }}?sub_category={{ $subCategory->id }}" class="badge badge-soft-primary">
                                        {{ $subCategory->services_count }} services
                                    </a>
                                </td>
                                <td>
                                    @if($subCategory->is_active)
                                        <span class="badge badge-soft-success">Active</span>
                                    @else
                                        <span class="badge badge-soft-danger">Disabled</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.categories.sub-categories.edit', [$category, $subCategory]) }}" class="btn btn-info btn-sm" title="Edit">
                                        <i class="ri-edit-line"></i>
                                    </a>
                                    <form action="{{ route('admin.categories.sub-categories.toggle', [$category, $subCategory]) }}" method="POST" class="d-inline">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-{{ $subCategory->is_active ? 'warning' : 'success' }} btn-sm">
                                            <i class="ri-{{ $subCategory->is_active ? 'close' : 'check' }}-line"></i>
                                            {{ $subCategory->is_active ? 'Disable' : 'Enable' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No sub-categories found. <a href="{{ route('admin.categories.sub-categories.create', $category) }}">Add one now</a>.</td>
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
        $('#sub-categories-table').DataTable({
            responsive: true,
            paging: true,
            searching: true,
            ordering: true
        });
    });
</script>
@endsection
@endsection
