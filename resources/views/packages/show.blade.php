@extends('layouts.app')

@section('title', $offer->title . ' | Get My Serv')

@push('styles')
<style>
    .package-detail-section {
        padding: 60px 0;
        background-color: var(--bg-color);
    }

    .package-detail-header {
        background: linear-gradient(135deg, #1a7a4a 0%, #23a85e 100%);
        color: #fff;
        border-radius: 16px;
        padding: 40px;
        margin-bottom: 32px;
        position: relative;
        overflow: hidden;
    }

    .package-detail-header::before {
        content: '';
        position: absolute;
        top: -40px;
        right: -40px;
        width: 200px;
        height: 200px;
        background: rgba(255,255,255,0.06);
        border-radius: 50%;
    }

    .package-detail-badge {
        display: inline-block;
        padding: 4px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        background: rgba(255,255,255,0.2);
        color: #fff;
        margin-bottom: 12px;
    }

    .package-detail-title {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .package-detail-desc {
        opacity: 0.85;
        margin-bottom: 0;
    }

    .services-card {
        background: #fff;
        border-radius: 12px;
        border: 1px solid #e8ebe8;
        overflow: hidden;
        margin-bottom: 24px;
    }

    .services-card-header {
        background: #f8faf8;
        padding: 16px 24px;
        border-bottom: 1px solid #e8ebe8;
        font-weight: 600;
        font-size: 15px;
        color: #333;
    }

    .service-item-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 24px;
        border-bottom: 1px solid #f0f2f0;
        gap: 12px;
    }

    .service-item-row:last-child {
        border-bottom: none;
    }

    .service-item-name {
        font-weight: 500;
        color: #222;
        flex: 1;
    }

    .service-item-package {
        font-size: 12px;
        font-weight: 600;
        padding: 3px 10px;
        border-radius: 20px;
        background: #e8f5ee;
        color: #1a7a4a;
        white-space: nowrap;
    }

    .pricing-card {
        background: #fff;
        border-radius: 12px;
        border: 1px solid #e8ebe8;
        overflow: hidden;
        margin-bottom: 24px;
    }

    .pricing-card-header {
        background: #f8faf8;
        padding: 16px 24px;
        border-bottom: 1px solid #e8ebe8;
        font-weight: 600;
        font-size: 15px;
        color: #333;
    }

    .pricing-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 13px 24px;
        border-bottom: 1px solid #f0f2f0;
        font-size: 15px;
    }

    .pricing-row:last-child {
        border-bottom: none;
    }

    .pricing-row.savings-row {
        color: #1a7a4a;
        font-weight: 600;
        background: #f0faf4;
    }

    .pricing-row.total-row {
        font-weight: 700;
        font-size: 17px;
        background: #f8faf8;
    }

    .pricing-row .strikethrough {
        text-decoration: line-through;
        color: #999;
        font-size: 13px;
        margin-left: 6px;
    }

    .btn-continue {
        background: linear-gradient(135deg, #1a7a4a, #23a85e);
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 14px 36px;
        font-size: 16px;
        font-weight: 600;
        width: 100%;
        cursor: pointer;
        transition: opacity 0.2s;
        text-decoration: none;
        display: inline-block;
        text-align: center;
    }

    .btn-continue:hover {
        opacity: 0.9;
        color: #fff;
    }

    .back-link {
        color: #1a7a4a;
        font-size: 14px;
        text-decoration: none;
    }

    .back-link:hover {
        text-decoration: underline;
    }
</style>
@endpush

@section('content')
<section class="package-detail-section">
    <div class="container">

        {{-- Back link --}}
        <div class="mb-3">
            <a href="{{ route('home') }}" class="back-link">
                <i class="fas fa-arrow-left me-1"></i> Back to Offers
            </a>
        </div>

        {{-- Error flash --}}
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Header --}}
        <div class="package-detail-header">
            <div class="package-detail-badge">{{ $offer->offer_type_label }}</div>
            <h1 class="package-detail-title">{{ $offer->title }}</h1>
            @if($offer->offer_detail)
                <p class="package-detail-desc">{{ $offer->offer_detail }}</p>
            @endif
        </div>

        <div class="row">
            {{-- Left: Services included --}}
            <div class="col-lg-7 col-md-12">
                <div class="services-card">
                    <div class="services-card-header">
                        <i class="fas fa-list-check me-2 text-success"></i> Services Included
                    </div>
                    @foreach($offer->services as $service)
                        @php
                            $pkg = $service->pivot->package ?: 'basic';
                            $price = $service->has_packages
                                ? ($pkg === 'premium'
                                    ? ($service->premium_package_actual_price ?? $service->premium_package_price)
                                    : ($service->basic_package_actual_price   ?? $service->basic_package_price))
                                : 0;
                        @endphp
                        <div class="service-item-row">
                            <span class="service-item-name">{{ $service->name }}</span>
                            <span class="service-item-package">{{ ucfirst($pkg) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Right: Pricing + Continue --}}
            <div class="col-lg-5 col-md-12">
                <div class="pricing-card">
                    <div class="pricing-card-header">
                        <i class="fas fa-tag me-2 text-success"></i> Price Summary
                    </div>

                    <div class="pricing-row">
                        <span>Price</span>
                        <span>AED {{ number_format($offerPrice, 2) }}</span>
                    </div>

                    @if($vatRate > 0)
                        <div class="pricing-row">
                            <span>VAT ({{ rtrim(rtrim(number_format($vatRate, 2), '0'), '.') }}%)</span>
                            <span>AED {{ number_format($vatAmount, 2) }}</span>
                        </div>
                    @endif

                    <div class="pricing-row total-row">
                        <span>Total</span>
                        <span class="text-success">AED {{ number_format($totalWithVat, 2) }}</span>
                    </div>
                </div>

                {{-- Continue / Login button --}}
                @auth
                    <form method="POST" action="{{ route('packages.checkout', $offer) }}">
                        @csrf
                        <button type="submit" class="btn-continue">
                            Continue <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn-continue">
                        Login to Continue <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                @endauth

                <p class="text-center text-muted small mt-3">
                    <i class="fas fa-shield-alt me-1"></i> Secure payment via Stripe
                </p>
            </div>
        </div>

    </div>
</section>
@endsection
