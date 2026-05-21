@extends('admin.layouts.app')
@section('title', 'Edit Sub-category')
@section('page-title', 'Edit Sub-category — ' . $category->name)

@section('content')
<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Categories</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.categories.sub-categories.index', $category) }}">{{ $category->name }}</a></li>
                <li class="breadcrumb-item active">Edit: {{ $subCategory->name }}</li>
            </ol>
        </nav>

        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Edit Sub-category</h4>

                <form action="{{ route('admin.categories.sub-categories.update', [$category, $subCategory]) }}" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <div class="row mb-3">
                        <label for="name" class="col-sm-2 col-form-label">Sub-category Name</label>
                        <div class="col-sm-10">
                            <input class="form-control @error('name') is-invalid @enderror" type="text" name="name" id="name" value="{{ old('name', $subCategory->name) }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="description" class="col-sm-2 col-form-label">Description</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="description" id="description" rows="3">{{ old('description', $subCategory->description) }}</textarea>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Icon</label>
                        <div class="col-sm-10">
                            @include('admin.partials.icon-picker', [
                                'inputName'   => 'icon',
                                'currentIcon' => old('icon', $subCategory->icon ?? ''),
                                'pickerId'    => 'subcat_edit',
                            ])
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="image" class="col-sm-2 col-form-label">Image</label>
                        <div class="col-sm-10">
                            @if($subCategory->image)
                            <div class="mb-2 d-flex align-items-center gap-3">
                                <img src="{{ Storage::url($subCategory->image) }}" alt="{{ $subCategory->name }}"
                                    class="rounded" style="max-height:100px;max-width:160px;object-fit:cover;">
                                <div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remove_image" id="remove_image" value="1">
                                        <label class="form-check-label text-danger small" for="remove_image">Remove current image</label>
                                    </div>
                                </div>
                            </div>
                            @endif
                            <input class="form-control @error('image') is-invalid @enderror" type="file" name="image" id="image"
                                accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                                onchange="previewImage(this, 'image-preview')">
                            @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <small class="text-muted">Upload a new image to replace the current one. JPEG, PNG, GIF or WebP. Max 2 MB.</small>
                            <div class="mt-2" id="image-preview" style="display:none;">
                                <img src="" alt="Preview" class="rounded" style="max-height:120px;max-width:200px;object-fit:cover;">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-10 offset-sm-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-save-line me-1"></i> Update Sub-category
                            </button>
                            <a href="{{ route('admin.categories.sub-categories.index', $category) }}" class="btn btn-secondary ms-2">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                preview.querySelector('img').src = e.target.result;
                preview.style.display = '';
            };
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.style.display = 'none';
        }
    }
</script>
@endsection
