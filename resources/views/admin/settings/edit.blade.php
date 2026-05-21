@extends('admin.layouts.app')

@section('title', 'General Settings')
@section('page-title', 'General Settings')

@section('content')

<div class="row">
    <div class="col-lg-8 col-xl-7">

        <form method="POST" action="{{ route('admin.settings.update') }}">
            @csrf
            @method('PUT')

            {{-- Contact Information --}}
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="ri-contacts-line me-2 text-primary"></i>Contact Information
                    </h5>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="ri-mail-line"></i></span>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email', $settings['email'] ?? '') }}"
                                placeholder="info@getmyserv.com">
                        </div>
                        @error('email')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Phone Number</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="ri-phone-line"></i></span>
                            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                value="{{ old('phone', $settings['phone'] ?? '') }}"
                                placeholder="+971 56 522 0202">
                        </div>
                        @error('phone')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold">Address</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="ri-map-pin-line"></i></span>
                            <textarea name="address" class="form-control @error('address') is-invalid @enderror"
                                rows="3" placeholder="Shams Business Center, Sharjah">{{ old('address', $settings['address'] ?? '') }}</textarea>
                        </div>
                        @error('address')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Tax Settings --}}
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="ri-percent-line me-2 text-primary"></i>Tax Settings
                    </h5>

                    <div class="mb-0">
                        <label class="form-label fw-semibold">VAT Rate (%)</label>
                        <div class="input-group" style="max-width:260px;">
                            <input type="number" name="vat_rate"
                                   class="form-control @error('vat_rate') is-invalid @enderror"
                                   value="{{ old('vat_rate', $settings['vat_rate'] ?? '0') }}"
                                   min="0" max="100" step="0.01"
                                   placeholder="0">
                            <span class="input-group-text">%</span>
                        </div>
                        <div class="form-text text-muted">
                            Set to 0 to disable VAT. Applied at checkout on the amount after offer discounts.
                        </div>
                        @error('vat_rate')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Social Media --}}
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="ri-share-line me-2 text-primary"></i>Social Media
                    </h5>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Facebook</label>
                        <div class="input-group">
                            <span class="input-group-text" style="color:#1877f2;"><i class="ri-facebook-fill"></i></span>
                            <input type="url" name="facebook" class="form-control @error('facebook') is-invalid @enderror"
                                value="{{ old('facebook', $settings['facebook'] ?? '') }}"
                                placeholder="https://www.facebook.com/yourpage">
                        </div>
                        @error('facebook')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">X (Twitter)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="ri-twitter-x-line"></i></span>
                            <input type="url" name="twitter" class="form-control @error('twitter') is-invalid @enderror"
                                value="{{ old('twitter', $settings['twitter'] ?? '') }}"
                                placeholder="https://x.com/yourhandle">
                        </div>
                        @error('twitter')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">LinkedIn</label>
                        <div class="input-group">
                            <span class="input-group-text" style="color:#0a66c2;"><i class="ri-linkedin-fill"></i></span>
                            <input type="url" name="linkedin" class="form-control @error('linkedin') is-invalid @enderror"
                                value="{{ old('linkedin', $settings['linkedin'] ?? '') }}"
                                placeholder="https://www.linkedin.com/in/yourprofile">
                        </div>
                        @error('linkedin')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold">Instagram</label>
                        <div class="input-group">
                            <span class="input-group-text" style="color:#e1306c;"><i class="ri-instagram-line"></i></span>
                            <input type="url" name="instagram" class="form-control @error('instagram') is-invalid @enderror"
                                value="{{ old('instagram', $settings['instagram'] ?? '') }}"
                                placeholder="https://www.instagram.com/yourhandle">
                        </div>
                        @error('instagram')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 mb-4">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="ri-save-line me-1"></i> Save Settings
                </button>
            </div>

        </form>

    </div>
</div>

@endsection
