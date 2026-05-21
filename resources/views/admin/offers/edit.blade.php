@extends('admin.layouts.app')
@section('title', 'Edit Offer')
@section('page-title', 'Edit Offer')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Edit Offer</h4>

                <form action="{{ route('admin.offers.update', $offer) }}" method="POST">
                    @csrf @method('PUT')

                    <div class="row mb-3">
                        <label for="offer_type" class="col-sm-2 col-form-label">Offer Type <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <select class="form-select @error('offer_type') is-invalid @enderror" name="offer_type" id="offer_type" required>
                                <option value="">— Select Offer Type —</option>
                                @foreach($offerTypes as $value => $label)
                                    <option value="{{ $value }}" {{ old('offer_type', $offer->offer_type) === $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('offer_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <small class="text-muted">Determines the badge displayed on the offer card.</small>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="title" class="col-sm-2 col-form-label">Offer Title <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <input class="form-control @error('title') is-invalid @enderror" type="text"
                                name="title" id="title"
                                placeholder="e.g. Freezone Licence Setup"
                                value="{{ old('title', $offer->title) }}" required>
                            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    {{-- Multi-service selector --}}
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Services <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <div id="service-rows-container"></div>
                            <button type="button" class="btn btn-outline-success btn-sm mt-1" id="add-service-row">
                                <i class="ri-add-line me-1"></i> Add Service
                            </button>
                            @if($errors->has('service_ids') || $errors->has('service_ids.*'))
                                <div class="text-danger small mt-1">
                                    {{ $errors->first('service_ids') ?: $errors->first('service_ids.*') }}
                                </div>
                            @endif
                            <small class="text-muted d-block mt-1">Select one or more services covered by this offer.</small>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="offer_price" class="col-sm-2 col-form-label">Offer Price <span class="text-danger">*</span></label>
                        <div class="col-sm-4">
                            <div class="input-group">
                                <span class="input-group-text">AED</span>
                                <input type="number" class="form-control @error('offer_price') is-invalid @enderror"
                                    name="offer_price" id="offer_price"
                                    placeholder="0.00" step="0.01" min="0"
                                    value="{{ old('offer_price', $offer->offer_price) }}" required>
                                @error('offer_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="offer_detail" class="col-sm-2 col-form-label">Offer Detail</label>
                        <div class="col-sm-10">
                            <textarea class="form-control @error('offer_detail') is-invalid @enderror"
                                name="offer_detail" id="offer_detail" rows="3"
                                placeholder="Brief description shown on the offer card">{{ old('offer_detail', $offer->offer_detail) }}</textarea>
                            @error('offer_detail') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="button_text" class="col-sm-2 col-form-label">Button Text <span class="text-danger">*</span></label>
                        <div class="col-sm-4">
                            <input class="form-control @error('button_text') is-invalid @enderror" type="text"
                                name="button_text" id="button_text"
                                placeholder="e.g. Get Started"
                                value="{{ old('button_text', $offer->button_text) }}" required>
                            @error('button_text') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Offer Period <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <div class="row g-2">
                                <div class="col-sm-4">
                                    <label class="form-label small text-muted">Start Date</label>
                                    <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                                        name="start_date" id="start_date"
                                        value="{{ old('start_date', $offer->start_date->format('Y-m-d')) }}" required>
                                    @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-sm-4">
                                    <label class="form-label small text-muted">End Date</label>
                                    <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                                        name="end_date" id="end_date"
                                        value="{{ old('end_date', $offer->end_date->format('Y-m-d')) }}" required>
                                    @error('end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Featured Card</label>
                        <div class="col-sm-10 d-flex align-items-center">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch"
                                    name="is_featured" id="is_featured" value="1"
                                    {{ old('is_featured', $offer->is_featured) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_featured">Display as Featured (highlighted card style)</label>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <label for="sort_order" class="col-sm-2 col-form-label">Sort Order</label>
                        <div class="col-sm-3">
                            <input type="number" class="form-control @error('sort_order') is-invalid @enderror"
                                name="sort_order" id="sort_order"
                                placeholder="0" min="0"
                                value="{{ old('sort_order', $offer->sort_order) }}">
                            @error('sort_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <small class="text-muted">Lower numbers appear first on the page.</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-10 offset-sm-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-save-line me-1"></i> Update Offer
                            </button>
                            <a href="{{ route('admin.offers.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@include('admin.offers._cascade_script', [
    'selectedServices' => $selectedServices,
])
@endsection
