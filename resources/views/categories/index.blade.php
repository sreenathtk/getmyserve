@extends('layouts.app')

@section('title', 'Browse Services | Get My Serv')

@section('meta')
    <meta name="description"
        content="Explore a wide range of service categories including finance, visa, company setup, legal, and more from trusted providers in Dubai.">
    <meta name="keywords" content="Dubai service categories, visa services, company setup UAE, finance services Dubai">
    <link rel="canonical" href="{{ route('categories.index') }}">
    <meta property="og:locale" content="en_IN" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="Browse Service Categories | Get My Serv" />
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
                        <h2>Services</h2>
                        <h4><a href="{{ url('/') }}">home</a> <span>// Services</span></h4>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Category Pill Tabs --}}
    <section class="Service-Info">
        <div class="container">
            <div class="visa-header">
                <div class="visa-header-left">
                    <h2>popular services</h2>
                </div>
            </div>

            <div class="pill-wrapper">
                <ul class="nav nav-pills pill-list row" id="pills-tab" role="tablist">
                    @foreach ($categories as $category)
                        @php $isActive = $activeCategory ? $activeCategory->id === $category->id : $loop->first; @endphp
                        <li class="nav-item col-lg-4 col-md-12 col-12" role="presentation">
                            <button class="nav-link {{ $isActive ? 'active' : '' }}"
                                id="cat-tab-{{ $category->id }}"
                                data-bs-toggle="pill"
                                data-bs-target="#cat-pane-{{ $category->id }}"
                                type="button" role="tab"
                                aria-controls="cat-pane-{{ $category->id }}"
                                aria-selected="{{ $isActive ? 'true' : 'false' }}">
                                <div class="service-info-wrapper">
                                    <div class="service-info-wrapper-icon">
                                        @if ($category->icon)
                                            <i class="{{ $category->icon }}"></i>
                                        @elseif ($category->image)
                                            <img src="{{ asset('storage/' . $category->image) }}"
                                                alt="{{ $category->name }}" class="img-fluid category-pill-img">
                                        @else
                                            <i class="fas fa-th-large"></i>
                                        @endif
                                    </div>
                                    <div class="service-info-wrapper-header">
                                        <h2>{{ $category->name }}</h2>
                                        @if ($category->description)
                                            <p>{{ $category->description }}</p>
                                        @endif
                                    </div>
                                </div>
                            </button>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>

    {{-- Tab Content: Sub-categories --}}
    <div class="tab-content" id="pills-tabContent">
        @foreach ($categories as $category)
            @php $isActive = $activeCategory ? $activeCategory->id === $category->id : $loop->first; @endphp
            <div class="tab-pane fade {{ $isActive ? 'show active' : '' }}"
                id="cat-pane-{{ $category->id }}"
                role="tabpanel"
                aria-labelledby="cat-tab-{{ $category->id }}"
                tabindex="0">

                <section class="visa-section">
                    <div class="container">

                        <div class="visa-header">
                            <div class="visa-header-left">
                                <h2>{{ $category->name }}</h2>
                            </div>
                            <div class="visa-header-right">
                                <a href="{{ route('services.index') }}" class="browse-link">
                                    Browse All services <i class="fas fa-arrow-to-right"></i>
                                </a>
                            </div>
                        </div>

                        @if ($category->subCategories->isNotEmpty())
                            <div class="visa-card-wrapper">
                                @foreach ($category->subCategories as $subCategory)
                                    <a href="{{ route('services.index', ['sub_category' => $subCategory->id]) }}">
                                        <div class="visa-card visa-c">
                                            <div class="visa-logo">
                                                @if ($subCategory->image)
                                                    <img src="{{ asset('storage/' . $subCategory->image) }}"
                                                        alt="{{ $subCategory->name }}" class="img-fluid">
                                                @else
                                                    <img src="/assets/images/services/sv-1.jpg.jpeg"
                                                        alt="{{ $subCategory->name }}" class="img-fluid">
                                                @endif
                                            </div>
                                            <h4>{{ $subCategory->name }}</h4>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted py-4">No sub-categories available yet.</p>
                        @endif

                    </div>
                </section>

            </div>
        @endforeach
    </div>

@endsection
