@extends('admin.layouts.app')
@section('title', 'Edit Service')
@section('page-title', 'Edit Service')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet">
<style>
    .ql-toolbar.ql-snow  { border-radius: .375rem .375rem 0 0; }
    .ql-container.ql-snow { border-radius: 0 0 .375rem .375rem; font-size: 14px;
                             height: auto !important; position: static !important; }
    .ql-editor            { min-height: 110px; height: auto !important; }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Edit Service</h4>

                <form action="{{ route('admin.services.update', $service) }}" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')

                    <div class="row mb-3">
                        <label for="category_id" class="col-sm-2 col-form-label">Category</label>
                        <div class="col-sm-10">
                            <select class="form-select" id="category_id" name="category_id">
                                <option value="">— Select Category —</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('category_id', $service->subCategory?->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="sub_category_id" class="col-sm-2 col-form-label">Sub-category</label>
                        <div class="col-sm-10">
                            <select class="form-select @error('sub_category_id') is-invalid @enderror" id="sub_category_id" name="sub_category_id">
                                <option value="">— Select Sub-category —</option>
                                @foreach($subCategories as $sc)
                                    <option value="{{ $sc->id }}" {{ old('sub_category_id', $service->sub_category_id) == $sc->id ? 'selected' : '' }}>
                                        {{ $sc->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('sub_category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="name" class="col-sm-2 col-form-label">Service Name</label>
                        <div class="col-sm-10">
                            <input class="form-control @error('name') is-invalid @enderror" type="text" name="name" id="name" value="{{ old('name', $service->name) }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="description" class="col-sm-2 col-form-label">Description</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="description" id="description" rows="3">{{ old('description', $service->description) }}</textarea>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Icon</label>
                        <div class="col-sm-10">
                            @include('admin.partials.icon-picker', [
                                'inputName'   => 'icon',
                                'currentIcon' => old('icon', $service->icon ?? ''),
                                'pickerId'    => 'service_edit',
                            ])
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="service_type" class="col-sm-2 col-form-label">Service Type</label>
                        <div class="col-sm-10">
                            <select class="form-select @error('service_type') is-invalid @enderror" name="service_type" id="service_type" required>
                                <option value="both" {{ old('service_type', $service->service_type) === 'both' ? 'selected' : '' }}>Book Now &amp; Enquire Now</option>
                                <option value="book_now" {{ old('service_type', $service->service_type) === 'book_now' ? 'selected' : '' }}>Book Now Only</option>
                                <option value="enquire_now" {{ old('service_type', $service->service_type) === 'enquire_now' ? 'selected' : '' }}>Enquire Now Only</option>
                            </select>
                            @error('service_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <small class="text-muted">Determines how customers can interact with this service.</small>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Estimated Completion</label>
                        <div class="col-sm-10">
                            <div class="input-group" style="max-width:320px;">
                                <input type="number" class="form-control @error('estimate_value') is-invalid @enderror"
                                    name="estimate_value" id="estimate_value"
                                    placeholder="e.g. 3" min="1"
                                    value="{{ old('estimate_value', $service->estimate_value) }}">
                                <select class="form-select @error('estimate_unit') is-invalid @enderror"
                                    name="estimate_unit" id="estimate_unit" style="max-width:120px;">
                                    <option value="">Unit</option>
                                    <option value="hours" {{ old('estimate_unit', $service->estimate_unit) === 'hours' ? 'selected' : '' }}>Hours</option>
                                    <option value="days"  {{ old('estimate_unit', $service->estimate_unit) === 'days'  ? 'selected' : '' }}>Days</option>
                                    <option value="weeks" {{ old('estimate_unit', $service->estimate_unit) === 'weeks' ? 'selected' : '' }}>Weeks</option>
                                </select>
                                @error('estimate_value') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                @error('estimate_unit')  <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <small class="text-muted">How long this service typically takes to complete. Leave blank if not applicable.</small>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="image" class="col-sm-2 col-form-label">Image</label>
                        <div class="col-sm-10">
                            @if($service->image)
                            <div class="mb-2 d-flex align-items-center gap-3">
                                <img src="{{ Storage::url($service->image) }}" alt="{{ $service->name }}"
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

                    {{-- Trending --}}
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label fw-semibold">Trending</label>
                        <div class="col-sm-10">
                            @php
                                $isTrending   = old('is_trending', $service->is_trending);
                                $limitReached = $trendingCount >= 5 && !$service->is_trending;
                            @endphp
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch"
                                    name="is_trending" id="is_trending" value="1"
                                    {{ $isTrending ? 'checked' : '' }}
                                    {{ $limitReached ? 'disabled' : '' }}>
                                <label class="form-check-label" for="is_trending">
                                    Set as Trending
                                    @if($limitReached)
                                        <span class="badge bg-warning text-dark ms-1">Limit reached (5/5)</span>
                                    @elseif($service->is_trending)
                                        <span class="badge bg-success ms-1">Active</span>
                                        <span class="text-muted small">({{ $trendingCount }}/5 slots used)</span>
                                    @else
                                        <span class="text-muted small">({{ $trendingCount }}/5 slots used)</span>
                                    @endif
                                </label>
                            </div>
                            @error('is_trending')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Package Pricing Section --}}
                    <hr class="my-4">

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label fw-semibold">Package Pricing</label>
                        <div class="col-sm-10 d-flex align-items-center gap-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch"
                                    name="has_packages" id="has_packages" value="1"
                                    {{ old('has_packages', $service->has_packages) ? 'checked' : '' }}>
                                <label class="form-check-label" for="has_packages">Enable Package Pricing</label>
                            </div>
                            <small class="text-muted">Enable if this service offers tiered packages.</small>
                        </div>
                    </div>

                    <div id="package_pricing_section" style="{{ old('has_packages', $service->has_packages) ? '' : 'display:none;' }}">

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Premium Package</label>
                            <div class="col-sm-10 d-flex align-items-center">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" name="has_premium_package" id="has_premium_package" value="1"
                                        {{ old('has_premium_package', $service->has_premium_package) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="has_premium_package">Enable Premium Package</label>
                                </div>
                            </div>
                        </div>

                        @php
                            $basicHasOffer   = old('basic_has_offer',   $service->basic_package_discount_type   ? '1' : '');
                            $premiumHasOffer = old('premium_has_offer', $service->premium_package_discount_type ? '1' : '');
                        @endphp

                        {{-- Basic Package --}}
                        <div class="card bg-light border mb-3">
                            <div class="card-body">
                                <h6 class="card-subtitle mb-3 text-muted fw-semibold">Basic Package</h6>
                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label">What's Included</label>
                                    <div class="col-sm-10">
                                        <textarea name="basic_package_description" id="basic_package_description" style="display:none;">{!! old('basic_package_description', $service->basic_package_description) !!}</textarea>
                                        <div id="basic_desc_editor" class="@error('basic_package_description') border border-danger @enderror"></div>
                                        @error('basic_package_description') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="basic_package_price" class="col-sm-2 col-form-label">Selling Price</label>
                                    <div class="col-sm-4">
                                        <div class="input-group">
                                            <span class="input-group-text">AED</span>
                                            <input type="number" class="form-control @error('basic_package_price') is-invalid @enderror"
                                                name="basic_package_price" id="basic_package_price"
                                                placeholder="0.00" step="0.01" min="0"
                                                value="{{ old('basic_package_price', $service->basic_package_price) }}"
                                                {{ $basicHasOffer ? 'readonly' : '' }}>
                                            @error('basic_package_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <small class="text-muted">Price customers pay. Calculated automatically when offer is enabled.</small>
                                    </div>
                                </div>
                                {{-- Basic Offer Toggle --}}
                                <div class="row mb-2">
                                    <div class="col-sm-10 offset-sm-2">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                id="basic_has_offer" name="basic_has_offer" value="1"
                                                {{ $basicHasOffer ? 'checked' : '' }}>
                                            <label class="form-check-label text-muted small" for="basic_has_offer">Enable offer / discount pricing</label>
                                        </div>
                                    </div>
                                </div>
                                {{-- Basic Offer Fields --}}
                                <div id="basic_offer_section" style="{{ $basicHasOffer ? '' : 'display:none;' }}">
                                    <div class="row mb-3">
                                        <label for="basic_package_actual_price" class="col-sm-2 col-form-label">Original Price</label>
                                        <div class="col-sm-4">
                                            <div class="input-group">
                                                <span class="input-group-text">AED</span>
                                                <input type="number" class="form-control @error('basic_package_actual_price') is-invalid @enderror"
                                                    name="basic_package_actual_price" id="basic_package_actual_price"
                                                    placeholder="0.00" step="0.01" min="0"
                                                    value="{{ old('basic_package_actual_price', $service->basic_package_actual_price) }}">
                                                @error('basic_package_actual_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                            <small class="text-muted">Regular price shown crossed-out on site</small>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label class="col-sm-2 col-form-label">Discount</label>
                                        <div class="col-sm-7">
                                            <div class="d-flex gap-2 align-items-start">
                                                <select class="form-select @error('basic_package_discount_type') is-invalid @enderror"
                                                    name="basic_package_discount_type" id="basic_package_discount_type" style="max-width:180px;">
                                                    <option value="percentage" {{ old('basic_package_discount_type', $service->basic_package_discount_type) === 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                                                    <option value="flat" {{ old('basic_package_discount_type', $service->basic_package_discount_type) === 'flat' ? 'selected' : '' }}>Flat Amount (AED)</option>
                                                </select>
                                                <div class="input-group" style="max-width:160px;">
                                                    <input type="number" class="form-control @error('basic_package_discount_value') is-invalid @enderror"
                                                        name="basic_package_discount_value" id="basic_package_discount_value"
                                                        placeholder="0" step="0.01" min="0"
                                                        value="{{ old('basic_package_discount_value', $service->basic_package_discount_value) }}">
                                                    <span class="input-group-text" id="basic_discount_unit">
                                                        {{ old('basic_package_discount_type', $service->basic_package_discount_type) === 'flat' ? 'AED' : '%' }}
                                                    </span>
                                                </div>
                                            </div>
                                            @error('basic_package_discount_type') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                            @error('basic_package_discount_value') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                            <small class="text-muted">Selling Price above will be calculated automatically</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Premium Package --}}
                        <div id="premium_package_section" class="card border-warning mb-3"
                            style="{{ old('has_premium_package', $service->has_premium_package) ? '' : 'display:none;' }}">
                            <div class="card-header bg-warning bg-opacity-10 border-warning">
                                <h6 class="mb-0 fw-semibold text-warning-emphasis">Premium Package</h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label">What's Included</label>
                                    <div class="col-sm-10">
                                        <textarea name="premium_package_description" id="premium_package_description" style="display:none;">{!! old('premium_package_description', $service->premium_package_description) !!}</textarea>
                                        <div id="premium_desc_editor" class="@error('premium_package_description') border border-danger @enderror"></div>
                                        @error('premium_package_description') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="premium_package_price" class="col-sm-2 col-form-label">Selling Price</label>
                                    <div class="col-sm-4">
                                        <div class="input-group">
                                            <span class="input-group-text">AED</span>
                                            <input type="number" class="form-control @error('premium_package_price') is-invalid @enderror"
                                                name="premium_package_price" id="premium_package_price"
                                                placeholder="0.00" step="0.01" min="0"
                                                value="{{ old('premium_package_price', $service->premium_package_price) }}"
                                                {{ $premiumHasOffer ? 'readonly' : '' }}>
                                            @error('premium_package_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <small class="text-muted">Price customers pay. Calculated automatically when offer is enabled.</small>
                                    </div>
                                </div>
                                {{-- Premium Offer Toggle --}}
                                <div class="row mb-2">
                                    <div class="col-sm-10 offset-sm-2">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                id="premium_has_offer" name="premium_has_offer" value="1"
                                                {{ $premiumHasOffer ? 'checked' : '' }}>
                                            <label class="form-check-label text-muted small" for="premium_has_offer">Enable offer / discount pricing</label>
                                        </div>
                                    </div>
                                </div>
                                {{-- Premium Offer Fields --}}
                                <div id="premium_offer_section" style="{{ $premiumHasOffer ? '' : 'display:none;' }}">
                                    <div class="row mb-3">
                                        <label for="premium_package_actual_price" class="col-sm-2 col-form-label">Original Price</label>
                                        <div class="col-sm-4">
                                            <div class="input-group">
                                                <span class="input-group-text">AED</span>
                                                <input type="number" class="form-control @error('premium_package_actual_price') is-invalid @enderror"
                                                    name="premium_package_actual_price" id="premium_package_actual_price"
                                                    placeholder="0.00" step="0.01" min="0"
                                                    value="{{ old('premium_package_actual_price', $service->premium_package_actual_price) }}">
                                                @error('premium_package_actual_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                            <small class="text-muted">Regular price shown crossed-out on site</small>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label class="col-sm-2 col-form-label">Discount</label>
                                        <div class="col-sm-7">
                                            <div class="d-flex gap-2 align-items-start">
                                                <select class="form-select @error('premium_package_discount_type') is-invalid @enderror"
                                                    name="premium_package_discount_type" id="premium_package_discount_type" style="max-width:180px;">
                                                    <option value="percentage" {{ old('premium_package_discount_type', $service->premium_package_discount_type) === 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                                                    <option value="flat" {{ old('premium_package_discount_type', $service->premium_package_discount_type) === 'flat' ? 'selected' : '' }}>Flat Amount (AED)</option>
                                                </select>
                                                <div class="input-group" style="max-width:160px;">
                                                    <input type="number" class="form-control @error('premium_package_discount_value') is-invalid @enderror"
                                                        name="premium_package_discount_value" id="premium_package_discount_value"
                                                        placeholder="0" step="0.01" min="0"
                                                        value="{{ old('premium_package_discount_value', $service->premium_package_discount_value) }}">
                                                    <span class="input-group-text" id="premium_discount_unit">
                                                        {{ old('premium_package_discount_type', $service->premium_package_discount_type) === 'flat' ? 'AED' : '%' }}
                                                    </span>
                                                </div>
                                            </div>
                                            @error('premium_package_discount_type') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                            @error('premium_package_discount_value') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                            <small class="text-muted">Selling Price above will be calculated automatically</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>{{-- /package_pricing_section --}}

                    <div class="row">
                        <div class="col-sm-10 offset-sm-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-save-line me-1"></i> Update Service
                            </button>
                            <a href="{{ route('admin.services.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
<script>
    // ── WYSIWYG: lazy init so editors never mount inside display:none ─────────
    const quillOptions = {
        theme: 'snow',
        modules: { toolbar: [['bold','italic','underline'], [{list:'ordered'},{list:'bullet'}], ['clean']] }
    };
    let basicQuill = null, premiumQuill = null;
    let basicEditorInit = false, premiumEditorInit = false;

    function initBasicEditor() {
        if (basicEditorInit) return;
        basicQuill = new Quill('#basic_desc_editor', quillOptions);
        const basicTa = document.getElementById('basic_package_description');
        if (basicTa.value) basicQuill.clipboard.dangerouslyPasteHTML(basicTa.value);
        basicQuill.on('text-change', () => { basicTa.value = basicQuill.root.innerHTML; });
        basicEditorInit = true;
    }
    function initPremiumEditor() {
        if (premiumEditorInit) return;
        premiumQuill = new Quill('#premium_desc_editor', quillOptions);
        const premiumTa = document.getElementById('premium_package_description');
        if (premiumTa.value) premiumQuill.clipboard.dangerouslyPasteHTML(premiumTa.value);
        premiumQuill.on('text-change', () => { premiumTa.value = premiumQuill.root.innerHTML; });
        premiumEditorInit = true;
    }
    function clearBasicEditor()   { if (basicEditorInit)   basicQuill.setText(''); }
    function clearPremiumEditor() { if (premiumEditorInit) premiumQuill.setText(''); }

    // Sync Quill → hidden textarea before form submit
    document.querySelector('form').addEventListener('submit', function () {
        if (basicEditorInit)   document.getElementById('basic_package_description').value   = basicQuill.root.innerHTML;
        if (premiumEditorInit) document.getElementById('premium_package_description').value = premiumQuill.root.innerHTML;
    });

    // Initialize immediately if sections are already visible on page load
    if (document.getElementById('package_pricing_section').style.display !== 'none') {
        initBasicEditor();
    }
    if (document.getElementById('premium_package_section').style.display !== 'none') {
        initPremiumEditor();
    }

    // ── Sub-category AJAX ────────────────────────────────────────────────────
    const subCategoryUrl = (categoryId) =>
        `{{ url('admin/categories') }}/${categoryId}/sub-categories-list`;

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

    const currentSubCategoryId = {{ $service->sub_category_id ?? 'null' }};

    document.getElementById('category_id').addEventListener('change', function () {
        const catId = this.value;
        const subSelect = document.getElementById('sub_category_id');
        subSelect.innerHTML = '<option value="">— Select Sub-category —</option>';
        if (!catId) return;
        fetch(subCategoryUrl(catId))
            .then(r => r.json())
            .then(data => {
                data.forEach(sc => {
                    const opt = document.createElement('option');
                    opt.value = sc.id;
                    opt.textContent = sc.name;
                    if (sc.id === currentSubCategoryId) opt.selected = true;
                    subSelect.appendChild(opt);
                });
            });
    });

    const selectedCategory = document.getElementById('category_id').value;
    if (selectedCategory) {
        document.getElementById('category_id').dispatchEvent(new Event('change'));
    }

    // ── Package Pricing outer toggle ─────────────────────────────────────────
    const packagesToggle = document.getElementById('has_packages');
    const packageSection  = document.getElementById('package_pricing_section');

    packagesToggle.addEventListener('change', function () {
        packageSection.style.display = this.checked ? '' : 'none';
        if (this.checked) {
            initBasicEditor();
        } else {
            document.getElementById('has_premium_package').checked = false;
            document.getElementById('premium_package_section').style.display = 'none';
            clearOfferSection('basic');
            clearOfferSection('premium');
            clearBasicEditor();
            clearPremiumEditor();
            document.getElementById('basic_package_price').value = '';
            document.getElementById('premium_package_price').value = '';
        }
    });

    // ── Premium Package inner toggle ─────────────────────────────────────────
    const premiumToggle = document.getElementById('has_premium_package');
    const premiumSection = document.getElementById('premium_package_section');

    premiumToggle.addEventListener('change', function () {
        premiumSection.style.display = this.checked ? '' : 'none';
        if (this.checked) {
            initPremiumEditor();
        } else {
            clearOfferSection('premium');
            clearPremiumEditor();
            document.getElementById('premium_package_price').value = '';
        }
    });

    // ── Offer section helpers ────────────────────────────────────────────────
    function clearOfferSection(pkg) {
        const toggle = document.getElementById(pkg + '_has_offer');
        if (toggle) toggle.checked = false;
        const section = document.getElementById(pkg + '_offer_section');
        if (section) section.style.display = 'none';
        ['actual_price', 'discount_value'].forEach(function (field) {
            const el = document.getElementById(pkg + '_package_' + field);
            if (el) el.value = '';
        });
        const typeEl = document.getElementById(pkg + '_package_discount_type');
        if (typeEl) typeEl.value = 'percentage';
        const unitEl = document.getElementById(pkg + '_discount_unit');
        if (unitEl) unitEl.textContent = '%';
        const priceEl = document.getElementById(pkg + '_package_price');
        if (priceEl) priceEl.readOnly = false;
    }

    function setupOfferSection(pkg) {
        const hasOfferToggle  = document.getElementById(pkg + '_has_offer');
        const offerSection    = document.getElementById(pkg + '_offer_section');
        const actualPriceEl   = document.getElementById(pkg + '_package_actual_price');
        const discountTypeEl  = document.getElementById(pkg + '_package_discount_type');
        const discountValueEl = document.getElementById(pkg + '_package_discount_value');
        const discountUnitEl  = document.getElementById(pkg + '_discount_unit');
        const priceEl         = document.getElementById(pkg + '_package_price');

        function calcPrice() {
            if (!hasOfferToggle.checked) return;
            const actual   = parseFloat(actualPriceEl.value);
            const type     = discountTypeEl.value;
            const discount = parseFloat(discountValueEl.value);
            if (!isNaN(actual) && !isNaN(discount)) {
                const result = type === 'percentage'
                    ? Math.max(0, actual - (actual * discount / 100))
                    : Math.max(0, actual - discount);
                priceEl.value = result.toFixed(2);
            } else {
                priceEl.value = '';
            }
        }

        hasOfferToggle.addEventListener('change', function () {
            offerSection.style.display = this.checked ? '' : 'none';
            priceEl.readOnly = this.checked;
            if (!this.checked) {
                actualPriceEl.value        = '';
                discountValueEl.value      = '';
                discountTypeEl.value       = 'percentage';
                discountUnitEl.textContent = '%';
                priceEl.readOnly           = false;
                priceEl.value              = '';
            }
        });

        discountTypeEl.addEventListener('change', function () {
            discountUnitEl.textContent = this.value === 'percentage' ? '%' : 'AED';
            calcPrice();
        });

        actualPriceEl.addEventListener('input', calcPrice);
        discountValueEl.addEventListener('input', calcPrice);

        if (hasOfferToggle.checked) {
            priceEl.readOnly = true;
            discountUnitEl.textContent = discountTypeEl.value === 'percentage' ? '%' : 'AED';
        }
    }

    setupOfferSection('basic');
    setupOfferSection('premium');
</script>
@endsection
@endsection
