@extends('layouts.app')

@section('title', 'Payment Successful | Get My Serv')

@push('styles')
<style>
    .success-section {
        padding: 60px 0;
        background-color: var(--bg-color);
    }
    .success-card {
        background: var(--color-white);
        padding: 50px 40px;
        border-radius: 12px;
        text-align: center;
        box-shadow: 0 4px 20px rgba(0,0,0,.07);
    }
    .success-icon {
        font-size: 4.5rem;
        color: var(--color-green);
        margin-bottom: 20px;
    }
    .success-card h2 {
        font-family: var(--font-secondary);
        font-size: var(--fs-24);
        font-weight: var(--fw-700);
        color: var(--color-dk-gray);
        margin-bottom: 10px;
    }
    .success-card p {
        font-family: var(--font-secondary);
        color: #666;
        margin-bottom: 24px;
    }
    .order-summary-box {
        background: var(--bg-color);
        border-radius: 8px;
        padding: 20px 24px;
        margin-bottom: 24px;
        text-align: left;
    }
    .order-summary-box h4 {
        font-family: var(--font-secondary);
        font-size: var(--fs-16);
        font-weight: var(--fw-600);
        color: var(--color-dk-gray);
        margin-bottom: 14px;
    }
    .order-line {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-family: var(--font-secondary);
        font-size: var(--fs-15);
        color: var(--color-dk-gray);
        padding: 6px 0;
        border-bottom: 1px solid var(--border-color);
    }
    .order-line:last-of-type { border-bottom: none; }
    .order-total {
        display: flex;
        justify-content: space-between;
        font-family: var(--font-secondary);
        font-weight: var(--fw-700);
        font-size: var(--fs-16);
        color: var(--color-dk-gray);
        margin-top: 12px;
        padding-top: 12px;
        border-top: 2px solid var(--color-dk-gray);
    }
    .order-total span { color: var(--color-green); }
    .order-id {
        font-size: .82rem;
        color: #999;
        margin-bottom: 24px;
    }
    .btn-home {
        display: inline-block;
        background-color: var(--color-green);
        color: var(--color-white);
        padding: 12px 32px;
        border-radius: 6px;
        font-family: var(--font-secondary);
        font-size: var(--fs-15);
        font-weight: var(--fw-600);
        text-decoration: none;
        transition: opacity .2s;
    }
    .btn-home:hover { opacity: .85; color: var(--color-white); }
</style>
@endpush

@section('content')

    {{-- Banner --}}
    <section class="about-banner ban-faq">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="about-banner-content">
                        <h2>Payment Successful</h2>
                        <h4>
                            <a href="{{ url('/') }}">home</a>
                            <span>// Payment</span>
                        </h4>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="success-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8 col-12">
                    <div class="success-card">

                        <div class="success-icon">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>

                        <h2>Thank you for your payment!</h2>
                        <p>Your order has been confirmed and is being processed. We will contact you shortly.</p>

                        @if($order)
                            <div class="order-summary-box">
                                <h4>Order Summary</h4>
                                @foreach($order->items as $item)
                                    <div class="order-line">
                                        <span>
                                            {{ $item['service_name'] }}
                                            @if($item['package'] !== 'standard')
                                                <small style="opacity:.7;">({{ ucfirst($item['package']) }} Package)</small>
                                            @endif
                                        </span>
                                        <strong>AED {{ number_format($item['price'], 0) }}</strong>
                                    </div>
                                @endforeach
                                <div class="order-total">
                                    <span style="color: var(--color-dk-gray);">Total Paid</span>
                                    <span>AED {{ number_format($order->amount, 0) }}</span>
                                </div>
                            </div>

                            <p class="order-id">Order Reference: #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</p>
                        @endif

                        <a href="{{ url('/') }}" class="btn-home">
                            <i class="fa-solid fa-house me-2"></i> Back to Home
                        </a>

                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
