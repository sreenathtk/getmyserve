@extends('layouts.app')

@section('title', 'Get My Serv | Dubai\'s Trusted Service Marketplace & Business Solutions')

@section('meta')
    <meta name="description"
        content="Connect with verified service providers in Dubai. Get My Serv helps you find reliable solutions for visas, company registration, tax, and more—all in one place.">
    <meta name="keywords" content="Dubai services, service providers UAE, visa services Dubai, online service booking">
    <link rel="canonical" href="#">
    <meta property="og:locale" content="en_IN" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="Get My Serv – Your Gateway to Trusted Services in Dubai" />
    <meta property="og:url" content="PAGE_URL" />
    <meta property="og:site_name" content="Drunken Duck" />
    <meta property="og:image" content="#" />
    <meta property="og:image:width" content="1200" />
    <meta property="og:image:height" content="550" />
    <meta property="og:description"
        content="Discover and connect with top-rated service providers for business, visa, and compliance solutions across Dubai." />
    <meta name="robots" content="index, follow" />
@endsection

@section('content')

    {{-- Home Banner --}}
    <section class="Home-Banner">
        <div class="container">
            <form action="">
                <div class="row">
                    <div class="col-lg-10 col-md-12 col-sm-12 col-12">
                        <div class="Home-Banner-Wrapper">
                            <div class="Home-Banner-Header">
                                <h2>your one-stop Solution for </h2>
                                <h3>professional services</h3>
                            </div>
                            <div class="Home-Banner-form">
                                <div class="home-banner-sub-form">
                                    <div class="row">
                                        <div class="col-lg-8 col-md-6">
                                            <div class="Home-Banner-form-input" style="position:relative;">
                                                <i class="far fa-search"></i>
                                                <input type="text" id="service-search"
                                                    placeholder="What service you want ?"
                                                    class="form-control" autocomplete="off">
                                                <ul id="search-dropdown" style="display:none;position:absolute;top:100%;left:0;right:0;background:#fff;border:1px solid #e0e0e0;border-radius:0 0 8px 8px;list-style:none;margin:0;padding:0;z-index:999;box-shadow:0 4px 16px rgba(0,0,0,.10);"></ul>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-6">
                                            <div class="Home-Banner-form-input">
                                                <input type="submit" class="form-control home-submit" value="search">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @if($trendingServices->isNotEmpty())
                                <div class="keywords">
                                    <span class="title">trending keywords</span>
                                    <ul>
                                        @foreach($trendingServices as $trending)
                                            <li><a href="{{ url('/services') }}?sub_category={{ $trending->sub_category_id }}">{{ $trending->name }}</a></li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>

    {{-- Special Offers --}}
    @if (session('cart_error'))
        <div class="container mt-3">
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-triangle-exclamation me-2"></i>{{ session('cart_error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif
    @if (session('cart_success'))
        <div class="container mt-3">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i>{{ session('cart_success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif
    @if ($offers->isNotEmpty())
        <section class="service-providers-offer">
            <div class="container">
                <div class="row mb-5 text-center">
                    <div class="col-12">
                        <span class="offer-subtitle d-block mb-2 text-uppercase fw-bold text-success"
                            style="letter-spacing: 2px;">Exclusive Deals Just for You</span>
                        <h2 class="offer-main-title fw-bold">Grab Our <span class="text-success offer-blink">Special
                                Offers</span></h2>
                        <p class="text-muted mt-2">Limited time deals on our most popular business services.</p>
                    </div>
                </div>
                <div class="row offer-grid-row">
                    @foreach ($offers as $offer)
                        <div class="col-lg-4 col-md-6 col-sm-12 mb-4 d-flex">
                            <div
                                class="service-offer-card {{ $offer->is_featured ? 'featured-offer' : '' }} w-100 position-relative text-center">
                                <div class="offer-badge-modern {{ $offer->offer_type }}">{{ $offer->offer_type_label }}
                                </div>
                                <h3 class="offer-title {{ $offer->is_featured ? 'text-white' : '' }}">
                                    {{ $offer->title }}
                                </h3>
                                <h6> 
                                    @foreach ($offer->services as $service)
                                        <span class="d-block">{{ $service->name }}</span>
                                    @endforeach
                                </h6>
                                <div class="offer-price-wrapper {{ $offer->is_featured ? 'text-white' : '' }}">
                                    <span
                                        class="offer-currency {{ $offer->is_featured ? 'text-white-50' : '' }}">Just</span>
                                    <span class="offer-price">{{ number_format($offer->offer_price, 0) }}</span>
                                    <span class="offer-currency {{ $offer->is_featured ? 'text-white-50' : '' }}">AED</span>
                                </div>
                                @if ($offer->offer_detail)
                                    <p
                                        class="offer-desc {{ $offer->is_featured ? 'text-white-50' : '' }} d-none d-md-block">
                                        {{ $offer->offer_detail }}</p>
                                @endif
                                <form method="POST" action="{{ route('cart.addOffer') }}" class="m-0">
                                    @csrf
                                    <input type="hidden" name="offer_id" value="{{ $offer->id }}">
                                    <button type="submit"
                                        class="btn-offer-cta claim-offer {{ $offer->is_featured ? 'btn-featured-cta' : '' }}">
                                        {{ $offer->button_text }} <i class="fas fa-arrow-right ms-2"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Find Your Services --}}
    <section class="Service-Info">
        <div class="container">
            <div class="visa-header">
                <div class="visa-header-left">
                    <h2>find your services</h2>
                </div>
            </div>
            <div class="pill-wrapper">
                <ul class="nav nav-pills pill-list row" id="pills-tab" role="tablist">
                    @foreach ($categories as $category)
                        <li class="nav-item col-lg-4 col-md-12 col-12" role="presentation">
                            <button class="nav-link {{ $loop->first ? 'active' : '' }}" id="cat-tab-{{ $category->id }}"
                                data-bs-toggle="pill" data-bs-target="#cat-pane-{{ $category->id }}" type="button"
                                role="tab" aria-controls="cat-pane-{{ $category->id }}"
                                aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                <div class="service-info-wrapper">
                                    <div class="service-info-wrapper-icon">
                                        @if ($category->icon)
                                            <i class="{{ $category->icon }}"></i>
                                        @elseif($category->image)
                                            <img src="{{ asset('storage/' . $category->image) }}"
                                                alt="{{ $category->name }}" class="img-fluid category-pill-img">
                                        @else
                                            <i class="fas fa-th-large"></i>
                                        @endif
                                    </div>
                                    <div class="service-info-wrapper-header">
                                        <h2>{{ $category->name }}</h2>
                                        <p>{{ $category->description }}</p>
                                    </div>
                                </div>
                            </button>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>

    {{-- Tab Content --}}
    <div class="tab-content" id="pills-tabContent">
        @foreach ($categories as $category)
            <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="cat-pane-{{ $category->id }}"
                role="tabpanel" aria-labelledby="cat-tab-{{ $category->id }}" tabindex="0">
                <section class="visa-section">
                    <div class="container">
                        <div class="visa-header">
                            <div class="visa-header-left">
                                <h2>{{ $category->name }}</h2>
                            </div>
                            <div class="visa-header-right">
                                <a href="{{ route('categories.index', ['category' => $category->id]) }}" class="browse-link">
                                    Browse All <i class="fas fa-arrow-to-right"></i>
                                </a>
                            </div>
                        </div>

                        <div class="visa-card-wrapper">
                            @foreach ($category->subCategories as $subCategory)
                                <a href="{{ route('services.index', ['sub_category' => $subCategory->id]) }}">
                                    <div class="visa-card visa-c">
                                        <div class="visa-logo">
                                            <img src="{{ asset('storage/' . $subCategory->image) }}"
                                                alt="{{ $subCategory->name }}" class="img-fluid">
                                        </div>
                                        <h4>{{ $subCategory->name }}</h4>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </section>
            </div>
        @endforeach
    </div>

    {{-- Trusted Service Providers --}}
    @if ($trustedProviders->isNotEmpty())
        <section class="trusted-service-providers">
            <div class="container">
                <div class="visa-header">
                    <div class="visa-header-left">
                        <h2>Trusted service providers</h2>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="swiper trust-slider">
                            <div class="swiper-wrapper">
                                @foreach ($trustedProviders as $provider)
                                    <div class="swiper-slide">
                                        <div class="trusted-service-card">
                                           
                                            <h2>{{ $provider->company_name }}</h2>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- How It Works --}}
    <section class="how-it-works-section">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="visa-header text-center mb-5 hiw-section-header">
                        <div class="w-100">
                            <h2 class="how-header">How It Works</h2>
                            <p class="hiw-subtitle">Your journey to successful service fulfillment in 6 simple steps.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center hiw-grid">
                <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                    <div class="hiw-card">
                        <div class="hiw-badge">1</div>
                        <div class="hiw-icon"><i class="fas fa-search"></i></div>
                        <h3 class="hiw-title">Browse Categories</h3>
                        <p class="hiw-desc">Explore various services such as finance, visas, and residency options..etc</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                    <div class="hiw-card">
                        <div class="hiw-badge">2</div>
                        <div class="hiw-icon"><i class="fas fa-balance-scale"></i></div>
                        <h3 class="hiw-title">Find the Service</h3>
                        <p class="hiw-desc">Check out our range of services and find the perfect match for your needs.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12 mb-4"> 
                    <div class="hiw-card">
                        <div class="hiw-badge">3</div>
                        <div class="hiw-icon"><i class="fas fa-paper-plane"></i></div>
                        <h3 class="hiw-title">Send Request/Payment</h3>
                        <p class="hiw-desc">Complete a simple form to connect with getmyserv and start the process.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                    <div class="hiw-card">
                        <div class="hiw-badge">4</div>
                        <div class="hiw-icon"><i class="fas fa-headset"></i></div>
                        <h3 class="hiw-title">Get Response</h3>
                        <p class="hiw-desc">Wait for a direct follow-up call from the Get My Serv team.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                    <div class="hiw-card">
                        <div class="hiw-badge">5</div>
                        <div class="hiw-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                        <h3 class="hiw-title">Payment & Documents</h3>
                        <p class="hiw-desc">The team handles all document and payment collection for you.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                    <div class="hiw-card">
                        <div class="hiw-badge">6</div>
                        <div class="hiw-icon"><i class="fas fa-tasks"></i></div>
                        <h3 class="hiw-title">Service Fulfillment</h3>
                        <p class="hiw-desc">Get My Serv manages the entire end-to-end process as your single point of
                            contact.</p>
                    </div>
                </div>
                <div class="col-lg-8 col-md-10 col-sm-12 mb-4 mt-2">
                    <div class="hiw-card hiw-success-card">
                        <div class="hiw-badge success-badge"><i class="fas fa-check"></i></div>
                        <div class="hiw-success-content">
                            <div class="hiw-icon success-icon"><i class="fas fa-smile-beam"></i></div>
                            <div class="hiw-success-text">
                                <h3 class="hiw-title">Happy Customer</h3>
                                <p class="hiw-desc">The journey ends with a successfully delivered service and total user
                                    satisfaction.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Working Process --}}
    <section class="how-we-work-section">
        <div class="container-fluid p-0">
            <div class="work-video-wrapper">
                <img src="/assets/images/Home/bg_image--1.webp" alt="Video Background" class="video-bg">
                <div class="video-overlay">
                    <div class="video-content text-center">
                        <h4>WORKING PROCESS</h4>
                        <h2>We help you to find our next service and advance your career today</h2>
                        <a href="#" class="play-btn"><i class="fas fa-play"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

@push('scripts')
<script>
(function () {
    const input    = document.getElementById('service-search');
    const dropdown = document.getElementById('search-dropdown');
    const form     = input.closest('form');
    const endpoint = '{{ route('search.services') }}';
    const basePath = '{{ url('/services') }}';
    let timer;
    let lastResults = [];

    input.addEventListener('input', function () {
        clearTimeout(timer);
        const q = this.value.trim();
        if (q.length < 2) { hide(); return; }

        timer = setTimeout(function () {
            fetch(endpoint + '?q=' + encodeURIComponent(q))
                .then(function (r) { return r.json(); })
                .then(function (results) {
                    lastResults = results;
                    if (!results.length) {
                        showError('No results found. Please try with some other keywords.');
                        return;
                    }
                    dropdown.innerHTML = results.map(function (s) {
                        return '<li data-url="' + basePath + '?sub_category=' + s.sub_category_id + '" '
                            + 'style="padding:10px 16px;cursor:pointer;font-size:14px;color:#333;border-bottom:1px solid #f0f0f0;">'
                            + s.name + '</li>';
                    }).join('');
                    dropdown.style.display = 'block';
                });
        }, 300);
    });

    dropdown.addEventListener('click', function (e) {
        const li = e.target.closest('li');
        if (li && li.dataset.url) {
            window.location.href = li.dataset.url;
        }
    });

    dropdown.addEventListener('mouseover', function (e) {
        const li = e.target.closest('li');
        if (li) li.style.background = '#f5f5f5';
    });

    dropdown.addEventListener('mouseout', function (e) {
        const li = e.target.closest('li');
        if (li) li.style.background = '';
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        handleSearch();
    });

    input.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') { hide(); return; }
        if (e.key === 'Enter')  { e.preventDefault(); handleSearch(); }
    });

    document.addEventListener('click', function (e) {
        if (!input.contains(e.target) && !dropdown.contains(e.target)) { hide(); }
    });

    function handleSearch() {
        const q = input.value.trim();
        if (!q) {
            showError('Please enter a keyword to search.');
            return;
        }
        if (lastResults.length) {
            window.location.href = basePath + '?sub_category=' + lastResults[0].sub_category_id;
            return;
        }
        // Results not yet fetched or cleared — fetch now and navigate to first
        fetch(endpoint + '?q=' + encodeURIComponent(q))
            .then(function (r) { return r.json(); })
            .then(function (results) {
                lastResults = results;
                if (!results.length) {
                    showError('No results found. Please try with some other keywords.');
                    return;
                }
                window.location.href = basePath + '?sub_category=' + results[0].sub_category_id;
            });
    }

    function showError(msg) {
        dropdown.innerHTML = '<li style="padding:10px 16px;font-size:13px;color:#dc3545;">' + msg + '</li>';
        dropdown.style.display = 'block';
    }

    function hide() {
        lastResults = [];
        dropdown.style.display = 'none';
        dropdown.innerHTML = '';
    }
}());
</script>
@endpush
