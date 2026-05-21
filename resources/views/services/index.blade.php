@extends('layouts.app')

@section('title', ($filterSubCategory ? $filterSubCategory->name . ' — ' : '') . 'Services | Get My Serv')

@section('meta')
    <meta name="description"
        content="Explore a wide range of services including visa processing, company registration, VAT filing, and more from trusted providers in Dubai.">
    <meta name="keywords" content="Dubai services, service providers UAE, visa services Dubai, online service booking">
    <link rel="canonical" href="{{ route('services.index') }}">
    <meta property="og:locale" content="en_IN" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="All-in-One Business & Visa Solutions" />
    <meta property="og:url" content="{{ request()->url() }}" />
    <meta property="og:description"
        content="Browse and connect with verified providers for visa, tax, and corporate services in Dubai." />
    <meta name="robots" content="index, follow" />
@endsection

@section('content')

    {{-- Banner --}}
    <section class="about-banner ban-service">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="about-banner-content">
                        <h2>{{ $filterSubCategory ? $filterSubCategory->name : 'Services' }}</h2>
                        <h4>
                            <a href="{{ url('/') }}">home</a>
                            <span>// <a href="{{ route('categories.index') }}">Services</a></span>
                            @if ($filterSubCategory)
                                <span>// <a href="{{ route('categories.index', ['category' => $filterSubCategory->category_id]) }}">{{ $filterSubCategory->category->name }}</a></span>
                                <span>// {{ $filterSubCategory->name }}</span>
                            @endif
                        </h4>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Services Grid --}}
    <section class="service-page">
        <div class="container">

            @php
                $allServices = $categories->flatMap(fn($cat) => $cat->subCategories->flatMap(fn($sc) => $sc->services));
            @endphp

            @if ($allServices->isEmpty())
                <div class="row">
                    <div class="col-12 py-5 text-center">
                        <p class="text-muted">No services available
                            @if ($filterSubCategory) in {{ $filterSubCategory->name }} @endif
                            at the moment.
                        </p>
                        <a href="{{ route('categories.index') }}" class="browse-link">
                            Browse All Categories <i class="fas fa-arrow-to-right"></i>
                        </a>
                    </div>
                </div>
            @else

                @foreach ($categories as $category)
                    @foreach ($category->subCategories as $subCategory)
                        @if ($subCategory->services->isNotEmpty())

                            {{-- Show subcategory heading only when listing all services (no filter) --}}
                            @if (! $filterSubCategory)
                                <div class="visa-header mt-4">
                                    <div class="visa-header-left">
                                        <h2>{{ $subCategory->name }}</h2>
                                    </div>
                                    <div class="visa-header-right">
                                        <a href="{{ route('services.index', ['sub_category' => $subCategory->id]) }}"
                                            class="browse-link">
                                            View All <i class="fas fa-arrow-to-right"></i>
                                        </a>
                                    </div>
                                </div>
                            @endif

                            <div class="row">
                                @foreach ($subCategory->services as $service)
                                    <div class="col-lg-4 col-md-4 col-sm-6 col-12">
                                        <div class="profile-box">

                                            <div class="text-div">

                                                <div class="profile-hds">
                                                    <h4>{{ $service->name }}</h4>
                                                </div>

                                                @if ($service->has_packages && $service->basic_package_price)
                                                    <div class="price">
                                                        <h2>AED {{ number_format($service->basic_package_price, 0) }}
                                                            @if ($service->basic_package_actual_price)
                                                                <span><s>AED {{ number_format($service->basic_package_actual_price, 0) }}</s></span>
                                                            @endif
                                                        </h2>
                                                    </div>
                                                    @if ($service->basic_package_discount_type)
                                                        <div class="offer-div">
                                                            <div class="offer-details">
                                                                @if ($service->basic_package_discount_type === 'percentage')
                                                                    <h1>Up to {{ number_format($service->basic_package_discount_value, 0) }}% off</h1>
                                                                @else
                                                                    <h1>AED {{ number_format($service->basic_package_discount_value, 0) }} off</h1>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="offer-div" style="visibility:hidden;">
                                                            <div class="offer-details"><h1>&nbsp;</h1></div>
                                                        </div>
                                                    @endif
                                                @else
                                                    <div class="price" style="visibility:hidden;">
                                                        <h2>&nbsp;</h2>
                                                    </div>
                                                    <div class="offer-div" style="visibility:hidden;">
                                                        <div class="offer-details"><h1>&nbsp;</h1></div>
                                                    </div>
                                                @endif

                                                <div class="btn-div">
                                                    @if (in_array($service->service_type, ['book_now', 'both']))
                                                        <div class="buttn">
                                                            <a href="{{ route('book.create', $service) }}">
                                                                Book Now<i class="fa fa-arrow-right"></i>
                                                            </a>
                                                        </div>
                                                    @endif
                                                    @if (in_array($service->service_type, ['enquire_now', 'both']))
                                                        <div class="buttn">
                                                            <a href="{{ route('enquiry.create', $service) }}">
                                                                Enquire Now<i class="fa fa-arrow-right"></i>
                                                            </a>
                                                        </div>
                                                    @endif
                                                </div>

                                            </div>

                                            <div class="rating">
                                                <div class="rating-details">
                                                    <ul>
                                                        @if ($service->average_rating)
                                                            <li>
                                                                <a href="{{ route('services.review', $service) }}"
                                                                   style="text-decoration:none;color:inherit;display:flex;align-items:center;gap:4px;">
                                                                    <i class="fas fa-star" style="color: rgb(17, 172, 86);"></i>
                                                                    <span>{{ $service->average_rating }}</span>
                                                                    ({{ $service->review_count }}+)
                                                                </a>
                                                            </li>
                                                        @else
                                                            <li>
                                                                <a href="{{ route('services.review', $service) }}"
                                                                   style="text-decoration:none;color:#aaa;font-size:.82rem;">
                                                                    <i class="far fa-star"></i> Write a review
                                                                </a>
                                                            </li>
                                                        @endif
                                                        @if ($service->estimate_value)
                                                            <li>
                                                                <i class="far fa-clock" style="color: rgb(178, 178, 187);"></i>
                                                                {{ $service->estimate_value }} {{ ucfirst($service->estimate_unit) }}
                                                            </li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                @endforeach
                            </div>

                        @endif
                    @endforeach
                @endforeach

            @endif

        </div>
    </section>

@endsection
