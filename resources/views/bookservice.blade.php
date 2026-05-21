@extends('layouts.app')

@section('title', $service->name . ' | GetMyServ')

@section('meta')
    <meta name="description" content="{{ Str::limit(strip_tags($service->description ?? ''), 160) }}">
    <link rel="canonical" href="{{ route('book.create', $service) }}">
    <style>
        /* Restore list markers inside Quill-generated package descriptions */
        .price-details ol,
        .price-details ul { padding-left: 1.4rem; }
        .price-details ol li {
            list-style-type: decimal !important;
            display: list-item !important;
        }
        .price-details ul li {
            list-style-type: disc !important;
            display: list-item !important;
        }
    </style>
@endsection

@section('content')

    {{-- Banner --}}
    <section class="about-banner ban-company">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="about-banner-content">
                        <h2>{{ $service->name }}</h2>
                        <h4>
                            <a href="{{ url('/') }}">home</a>
                            <span>// <a href="{{ route('services.index') }}">Services</a></span>
                            @if ($service->subCategory)
                                <span>// <a href="{{ route('services.index', ['sub_category' => $service->sub_category_id]) }}">{{ $service->subCategory->name }}</a></span>
                            @endif
                            <span>// {{ $service->name }}</span>
                        </h4>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if(session('cart_success'))
        <div class="container mt-3">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i>{{ session('cart_success') }}
                — <a href="{{ route('cart.index') }}" class="alert-link">View Cart</a>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    {{-- Company Profile Section --}}
    <section class="company-profile-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="profile-section">

                        {{-- Service header --}}
                        <div class="about-content">
                            @if ($service->image)
                                <div class="com-img">
                                    <img class="img-fluid" src="{{ asset('storage/' . $service->image) }}" alt="{{ $service->name }}">
                                </div>
                            @endif
                            <div class="contents">
                                <h2>{{ $service->name }}</h2>
                                <div class="icons">
                                    @if ($service->average_rating)
                                        <div class="stars">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i class="fa fa-star" aria-hidden="true"
                                                    style="color: {{ $i <= round($service->average_rating) ? 'rgb(17,172,86)' : '#ccc' }};"></i>
                                            @endfor
                                            <h2><a href="#enquiry-form">({{ $service->review_count }}+ Reviews)</a></h2>
                                        </div>
                                    @endif
                                    @if ($service->estimate_value)
                                        <div style="display:flex;align-items:center;gap:6px;font-size:15px;color:#504e4e;">
                                            <i class="far fa-clock" style="color:rgb(178,178,187);"></i>
                                            {{ $service->estimate_value }} {{ ucfirst($service->estimate_unit) }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Pricing plans --}}
                        @if ($service->has_packages)
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                <div class="row">

                                    {{-- Basic Package --}}
                                    <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                                        <div class="pricing-plan">
                                            <div class="price-head">
                                                <h1>Basic Package</h1>
                                                <h2>Starter Plan</h2>
                                            </div>
                                            @if ($service->basic_package_description)
                                                <div class="price-details">
                                                    {!! $service->basic_package_description !!}
                                                </div>
                                            @endif
                                            @if ($service->basic_package_price)
                                                <div class="price">
                                                    <h1>AED {{ number_format($service->basic_package_price, 0) }}
                                                        @if ($service->basic_package_actual_price)
                                                            <span>AED {{ number_format($service->basic_package_actual_price, 0) }}</span>
                                                            @if ($service->basic_package_discount_type === 'percentage')
                                                                {{ number_format($service->basic_package_discount_value, 0) }}%
                                                            @endif
                                                        @endif
                                                    </h1>
                                                </div>
                                            @endif
                                            <div class="buying-btn mt-3">
                                                <form method="POST" action="{{ route('cart.add') }}" class="m-0">
                                                    @csrf
                                                    <input type="hidden" name="service_id" value="{{ $service->id }}">
                                                    <input type="hidden" name="package" value="basic">
                                                    <button type="submit" class="btn btn-buying">Book this</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Premium Package --}}
                                    @if ($service->has_premium_package)
                                        <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                                            <div class="pricing-plan">
                                                <div class="price-head">
                                                    <h1>Premium Package</h1>
                                                    <h2>Premium Plan</h2>
                                                </div>
                                                @if ($service->premium_package_description)
                                                    <div class="price-details">
                                                        {!! $service->premium_package_description !!}
                                                    </div>
                                                @endif
                                                @if ($service->premium_package_price)
                                                    <div class="price">
                                                        <h1>AED {{ number_format($service->premium_package_price, 0) }}
                                                            @if ($service->premium_package_actual_price)
                                                                <span>AED {{ number_format($service->premium_package_actual_price, 0) }}</span>
                                                                @if ($service->premium_package_discount_type === 'percentage')
                                                                    {{ number_format($service->premium_package_discount_value, 0) }}%
                                                                @endif
                                                            @endif
                                                        </h1>
                                                    </div>
                                                @endif
                                                <div class="buying-btn mt-3">
                                                    <form method="POST" action="{{ route('cart.add') }}" class="m-0">
                                                        @csrf
                                                        <input type="hidden" name="service_id" value="{{ $service->id }}">
                                                        <input type="hidden" name="package" value="premium">
                                                        <button type="submit" class="btn btn-buying">Book this</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                </div>
                            </div>
                        @else
                            {{-- No packages: show description + direct book button --}}
                            @if ($service->description)
                                <p style="font-size:15px;color:#504e4e;line-height:1.7;">{!! $service->description !!}</p>
                            @endif
                            <form method="POST" action="{{ route('cart.add') }}" class="m-0 mb-4">
                                @csrf
                                <input type="hidden" name="service_id" value="{{ $service->id }}">
                                <input type="hidden" name="package" value="basic">
                                <button type="submit" class="btn btn-buying">Book Now</button>
                            </form>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Addon Services --}}
    @if ($addonServices->isNotEmpty())
        <section class="addon-section">
            <div class="container">
                <div class="visa-header">
                    <div class="visa-header-left">
                        <h2>Add-on Services</h2>
                    </div>
                </div>
                <div class="row">
                    @foreach ($addonServices as $addon)
                        <div class="col-lg-6 col-12">
                            <div class="addons-cart">
                                <li>
                                    <h3>{{ $addon->name }}</h3>
                                    <span>
                                        <a href="{{ route('enquiry.create', $addon) }}">
                                            Enquire Now <i class="fal fa-arrow-right"></i>
                                        </a>
                                    </span>
                                </li>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Enquiry Form --}}
    <section class="company-profile-info-section" id="enquiry-form">
        <div class="container">
            <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                <div class="remarkss">
                    <div class="remarks-header">
                        <h4>Book / Enquire Now</h4>
                    </div>
                    <div class="remarks-form">
                        <form action="{{ route('book.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="service_id" value="{{ $service->id }}">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="remark-item">
                                        <input type="text" name="first_name" placeholder="First Name *"
                                            class="remark-input @error('first_name') border border-danger @enderror"
                                            value="{{ old('first_name') }}" required>
                                        @error('first_name')
                                            <div class="text-danger small" style="margin-top:-15px;margin-bottom:10px;">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="remark-item">
                                        <input type="text" name="last_name" placeholder="Last Name *"
                                            class="remark-input @error('last_name') border border-danger @enderror"
                                            value="{{ old('last_name') }}" required>
                                        @error('last_name')
                                            <div class="text-danger small" style="margin-top:-15px;margin-bottom:10px;">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="remark-item">
                                        <input type="email" name="email" placeholder="Email Address *"
                                            class="remark-input @error('email') border border-danger @enderror"
                                            value="{{ old('email') }}" required>
                                        @error('email')
                                            <div class="text-danger small" style="margin-top:-15px;margin-bottom:10px;">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="remark-item">
                                        <input type="tel" name="phone" placeholder="Phone Number *"
                                            class="remark-input @error('phone') border border-danger @enderror"
                                            value="{{ old('phone') }}" required>
                                        @error('phone')
                                            <div class="text-danger small" style="margin-top:-15px;margin-bottom:10px;">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="remark-item">
                                        <input type="tel" name="whatsapp" placeholder="WhatsApp Number"
                                            class="remark-input" value="{{ old('whatsapp') }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="remark-item">
                                        <input type="text" name="location" placeholder="Location"
                                            class="remark-input" value="{{ old('location') }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="remark-item">
                                        <input type="text" name="language" placeholder="Preferred Language"
                                            class="remark-input" value="{{ old('language') }}">
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="remark-item">
                                        <textarea name="remarks" rows="3" placeholder="Remarks"
                                            class="remark-address">{{ old('remarks') }}</textarea>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <button type="submit" class="contact-btn">Send Request</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
