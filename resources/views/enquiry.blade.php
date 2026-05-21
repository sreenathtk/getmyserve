@extends('layouts.app')

@section('title', $service->name . ' — Enquiry | GetMyServ')

@section('meta')
    <meta name="description" content="Submit your enquiry for {{ $service->name }} and get connected with the best service providers.">
    <link rel="canonical" href="{{ route('enquiry.create', $service) }}">
@endsection

@section('content')

    {{-- Banner --}}
    <section class="about-banner ban-enquiry">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="about-banner-content">
                        <h2>Enquiry Form</h2>
                        <h4>
                            <a href="{{ url('/') }}">home</a>
                            <span>// Enquiry Form</span>
                        </h4>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Enquiry Form Section --}}
    <section class="ue-section py-5">
        <div class="container">
            <div class="row g-5 align-items-center">

                <div class="col-lg-10 col-md-12 mx-auto">
                    <div class="ue-form-wrapper">
                        <div class="ue-form-card">

                            <div class="ue-form-header text-center mb-4">
                                <h3 class="fw-bold mb-2">Get Expert Help Today</h3>
                                <p class="text-muted m-0">Fill out the form below. We respond within <strong>24 hours</strong>.</p>
                            </div>

                            @if ($errors->any())
                                <div class="alert alert-danger mb-3">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('enquiry.store') }}" method="POST" class="ue-form">
                                @csrf
                                <input type="hidden" name="service_id" value="{{ $service->id }}">

                                <div class="row g-3">

                                    {{-- First Name & Last Name --}}
                                    <div class="col-md-6">
                                        <div class="ue-form-group input-with-icon">
                                            <i class="far fa-user"></i>
                                            <input type="text" name="first_name"
                                                class="form-control @error('first_name') is-invalid @enderror"
                                                placeholder="First Name"
                                                value="{{ old('first_name', $firstName) }}"
                                                required>
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
                                                value="{{ old('last_name', $lastName) }}"
                                                required>
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
                                                value="{{ old('email', $user->email) }}"
                                                required>
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
                                                value="{{ old('phone', $user->phone) }}"
                                                required>
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
                                                placeholder="WhatsApp Number"
                                                value="{{ old('whatsapp') }}">
                                        </div>
                                    </div>

                                    {{-- Service (disabled — set by URL) --}}
                                    <div class="col-12">
                                        <div class="ue-form-group input-with-icon">
                                            <i class="fas fa-briefcase"></i>
                                            <select class="form-select" disabled>
                                                <option selected>{{ $service->name }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Location --}}
                                    <div class="col-12">
                                        <div class="ue-form-group input-with-icon">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <input type="text" name="location"
                                                class="form-control"
                                                placeholder="Your Location"
                                                value="{{ old('location') }}">
                                        </div>
                                    </div>

                                    {{-- Language --}}
                                    <div class="col-12">
                                        <div class="ue-form-group input-with-icon">
                                            <i class="fas fa-language"></i>
                                            <select name="language" class="form-select @error('language') is-invalid @enderror">
                                                <option value="" disabled {{ old('language') ? '' : 'selected' }}>Choose Your Language</option>
                                                <option value="English" {{ old('language') === 'English' ? 'selected' : '' }}>English</option>
                                                <option value="Arabic" {{ old('language') === 'Arabic' ? 'selected' : '' }}>Arabic</option>
                                            </select>
                                            @error('language')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Remarks --}}
                                    <div class="col-12">
                                        <div class="ue-form-group input-with-icon">
                                            <i class="fas fa-comment-alt" style="top: 18px;"></i>
                                            <textarea name="remarks"
                                                class="form-control"
                                                placeholder="Additional Remarks / Requirements"
                                                rows="3">{{ old('remarks') }}</textarea>
                                        </div>
                                    </div>

                                    {{-- Submit --}}
                                    <div class="col-12 mt-4">
                                        <button class="btn btn-ue-submit w-100" type="submit">
                                            SEND REQUEST <i class="fas fa-paper-plane ms-2"></i>
                                        </button>
                                    </div>

                                    <div class="col-12 text-center mt-3">
                                        <p class="text-muted small m-0">
                                            Need immediate assistance?
                                            <a href="#" class="text-success fw-bold text-decoration-none">
                                                <i class="fab fa-whatsapp"></i> Chat on WhatsApp
                                            </a>
                                        </p>
                                    </div>

                                </div>
                            </form>
                        </div>

                        <div class="ue-blob-bg"></div>
                    </div>
                </div>

            </div>
        </div>
    </section>

@endsection
