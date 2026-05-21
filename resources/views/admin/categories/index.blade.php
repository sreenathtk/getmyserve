@extends('admin.layouts.app')
@section('title', 'Categories')
@section('page-title', 'Categories Management')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title">All Categories</h4>
                    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-sm">
                        <i class="ri-add-line me-1"></i> Add Category
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-centered mb-0" id="categories-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Category Name</th>
                                <th>Description</th>
                                <th>Sub-categories</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $index => $category)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><strong>{{ $category->name }}</strong></td>
                                <td>{{ Str::limit($category->description, 50) }}</td>
                                <td>
                                    <a href="{{ route('admin.categories.sub-categories.index', $category) }}" class="badge badge-soft-info">
                                        {{ $category->sub_categories_count }} sub-categories
                                    </a>
                                </td>
                                <td>
                                    @if($category->is_active)
                                        <span class="badge badge-soft-success">Active</span>
                                    @else
                                        <span class="badge badge-soft-danger">Disabled</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.categories.sub-categories.index', $category) }}" class="btn btn-secondary btn-sm" title="Manage Sub-categories">
                                        <i class="ri-list-unordered"></i>
                                    </a>
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-info btn-sm" title="Edit">
                                        <i class="ri-edit-line"></i>
                                    </a>
                                    <form action="{{ route('admin.categories.toggle', $category) }}" method="POST" class="d-inline">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-{{ $category->is_active ? 'warning' : 'success' }} btn-sm">
                                            <i class="ri-{{ $category->is_active ? 'close' : 'check' }}-line"></i>
                                            {{ $category->is_active ? 'Disable' : 'Enable' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No categories found</td>
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
        $('#categories-table').DataTable({
            responsive: true,
            paging: true,
            searching: true,
            ordering: true
        });
    });
</script>
@endsection
@endsection
