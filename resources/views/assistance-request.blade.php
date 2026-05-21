@extends('layouts.app')

@section('title', 'Assistance Request | Get My Serv')

@section('meta')
    <meta name="description" content="Need help? Submit an assistance request and our team will get back to you within 24 hours.">
    <link rel="canonical" href="{{ route('assistance-request.create') }}">
@endsection

@section('content')

    {{-- Banner --}}
    <section class="about-banner ban-enquiry">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="about-banner-content">
                        <h2>Assistance Request</h2>
                        <h4>
                            <a href="{{ url('/') }}">home</a>
                            <span>// Assistance Request</span>
                        </h4>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Form Section --}}
    <section class="ue-section py-5">
        <div class="container">
            <div class="row g-5 align-items-center">

                <div class="col-lg-10 col-md-12 mx-auto">
                    <div class="ue-form-wrapper">
                        <div class="ue-form-card">

                            <div class="ue-form-header text-center mb-4">
                                <h3 class="fw-bold mb-2">Request Assistance</h3>
                                <p class="text-muted m-0">Fill out the form below. Our team will respond within <strong>24 hours</strong>.</p>
                            </div>

                            @if(session('success'))
                            <div class="alert alert-success d-flex align-items-center gap-2 mb-4" role="alert">
                                <i class="fas fa-check-circle fs-5"></i>
                                <div>{{ session('success') }}</div>
                            </div>
                            @endif

                            <form action="{{ route('assistance-request.store') }}" method="POST" class="ue-form">
                                @csrf
                                <div class="row g-3">

                                    {{-- First Name & Last Name --}}
                                    <div class="col-md-6">
                                        <div class="ue-form-group input-with-icon">
                                            <i class="far fa-user"></i>
                                            <input type="text" name="first_name"
                                                   class="form-control @error('first_name') is-invalid @enderror"
                                                   placeholder="First Name"
                                                   value="{{ old('first_name') }}" required>
                                            @error('first_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="ue-form-group input-with-icon">
                                            <i class="far fa-user"></i>
                                            <input type="text" name="last_name"
                                                   class="form-control @error('last_name') is-invalid @enderror"
                                                   placeholder="Last Name"
                                                   value="{{ old('last_name') }}" required>
                                            @error('last_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Email --}}
                                    <div class="col-12">
                                        <div class="ue-form-group input-with-icon">
                                            <i class="far fa-envelope"></i>
                                            <input type="email" name="email"
                                                   class="form-control @error('email') is-invalid @enderror"
                                                   placeholder="Email Address"
                                                   value="{{ old('email') }}" required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Phone & WhatsApp --}}
                                    <div class="col-md-6">
                                        <div class="ue-form-group input-with-icon">
                                            <i class="fas fa-phone-plus"></i>
                                            <input type="tel" name="phone"
                                                   class="form-control @error('phone') is-invalid @enderror"
                                                   placeholder="Phone Number"
                                                   value="{{ old('phone') }}" required>
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="ue-form-group input-with-icon">
                                            <i class="fab fa-whatsapp"></i>
                                            <input type="tel" name="whatsapp"
                                                   class="form-control"
                                                   placeholder="WhatsApp Number (optional)"
                                                   value="{{ old('whatsapp') }}">
                                        </div>
                                    </div>

                                    {{-- Service (optional) --}}
                                    <div class="col-12">
                                        <div class="ue-form-group input-with-icon">
                                            <i class="fas fa-briefcase"></i>
                                            <select name="service_id" class="form-select">
                                                <option value="">Choose a Service (optional)</option>
                                                @foreach($services as $service)
                                                <option value="{{ $service->id }}"
                                                        {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                                    {{ $service->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Location --}}
                                    <div class="col-12">
                                        <div class="ue-form-group input-with-icon">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <input type="text" name="location"
                                                   class="form-control"
                                                   placeholder="Your Location (optional)"
                                                   value="{{ old('location') }}">
                                        </div>
                                    </div>

                                    {{-- Language --}}
                                    <div class="col-12">
                                        <div class="ue-form-group input-with-icon">
                                            <i class="fas fa-language"></i>
                                            <select name="language" class="form-select">
                                                <option value="">Choose Your Language (optional)</option>
                                                @foreach(['English', 'Arabic', 'Hindi', 'Urdu'] as $lang)
                                                <option value="{{ $lang }}"
                                                        {{ old('language') === $lang ? 'selected' : '' }}>
                                                    {{ $lang }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Remarks --}}
                                    <div class="col-12">
                                        <div class="ue-form-group input-with-icon">
                                            <i class="fas fa-comment-alt" style="top: 18px;"></i>
                                            <textarea name="remarks" class="form-control"
                                                      placeholder="Additional Remarks / How can we help you?"
                                                      rows="3">{{ old('remarks') }}</textarea>
                                        </div>
                                    </div>

                                    {{-- Submit --}}
                                    <div class="col-12 mt-4">
                                        <button class="btn btn-ue-submit w-100" type="submit">
                                            SEND REQUEST <i class="fas fa-paper-plane ms-2"></i>
                                        </button>
                                    </div>

                                    {{-- Quick Contact --}}
                                    <div class="col-12 text-center mt-3">
                                        <p class="text-muted small m-0">Need immediate assistance?
                                            <a href="#" class="text-success fw-bold text-decoration-none">
                                                <i class="fab fa-whatsapp"></i> Chat on WhatsApp
                                            </a>
                                        </p>
                                    </div>

                                </div>
                            </form>

                        </div>

                        {{-- Decorative Blob Background --}}
                        <div class="ue-blob-bg"></div>
                    </div>
                </div>

            </div>
        </div>
    </section>

@endsection
