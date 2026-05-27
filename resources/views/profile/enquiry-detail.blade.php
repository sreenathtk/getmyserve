@extends('layouts.app')

@section('title', 'Service Request #' . str_pad($enquiry->id, 5, '0', STR_PAD_LEFT) . ' | Get My Serv')

@push('styles')
<style>
    .request-detail-section { padding: 60px 0; background-color: var(--bg-color); }
    .request-card {
        background: var(--color-white);
        padding: 36px 40px;
        margin-bottom: 24px;
    }
    .request-section-title {
        font-family: var(--font-secondary);
        font-size: var(--fs-16);
        font-weight: var(--fw-700);
        color: var(--color-dk-gray);
        margin-bottom: 16px;
        padding-bottom: 10px;
        border-bottom: 1px solid var(--border-color);
    }
    .detail-row {
        display: flex;
        gap: 12px;
        margin-bottom: 10px;
        font-family: var(--font-secondary);
        font-size: var(--fs-14);
    }
    .detail-label {
        min-width: 140px;
        color: var(--color-lt-gray, #888);
        font-weight: var(--fw-600);
    }
    .detail-value { color: var(--color-dk-gray); font-weight: var(--fw-500); }
    .status-badge {
        display: inline-block;
        padding: 5px 16px;
        border-radius: 20px;
        font-size: .85rem;
        font-weight: 600;
        font-family: var(--font-secondary);
    }
    .status-pending         { background:#fff3cd; color:#856404; }
    .status-in_progress     { background:#cff4fc; color:#055160; }
    .status-under_processing{ background:#e0d7f8; color:#4a2c91; }
    .status-completed       { background:#d1fae5; color:#065f46; }
    .status-resolved        { background:#d1fae5; color:#065f46; }

    /* Timeline */
    .timeline { position: relative; padding-left: 28px; }
    .timeline::before {
        content: '';
        position: absolute;
        left: 8px; top: 8px; bottom: 0;
        width: 2px;
        background: var(--border-color);
    }
    .timeline-item { position: relative; margin-bottom: 24px; }
    .timeline-dot {
        position: absolute;
        left: -24px;
        top: 4px;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: var(--color-green);
        border: 2px solid var(--color-white);
        box-shadow: 0 0 0 2px var(--color-green);
    }
    .timeline-meta {
        font-family: var(--font-secondary);
        font-size: 12px;
        color: var(--color-lt-gray, #888);
        margin-bottom: 4px;
    }
    .timeline-note {
        font-family: var(--font-secondary);
        font-size: var(--fs-14);
        color: var(--color-dk-gray);
        background: #f9fafb;
        border: 1px solid var(--border-color);
        border-radius: 6px;
        padding: 10px 14px;
    }
    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: none;
        border: 1px solid var(--border-color);
        color: var(--color-dk-gray);
        padding: 9px 20px;
        border-radius: 6px;
        font-family: var(--font-secondary);
        font-size: var(--fs-14);
        font-weight: var(--fw-600);
        text-decoration: none;
        transition: all .2s;
    }
    .btn-back:hover { border-color: var(--color-green); color: var(--color-green); }
    @media (max-width: 576px) {
        .request-card { padding: 24px 16px; }
        .detail-label { min-width: 110px; }
    }
</style>
@endpush

@section('content')

    {{-- Banner --}}
    <section class="about-banner ban-faq">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="about-banner-content">
                        <h2>Service Request</h2>
                        <h4>
                            <a href="{{ url('/') }}">Home</a>
                            <span>// </span>
                            <a href="{{ route('profile.show') }}">My Profile</a>
                            <span>// #{{ str_pad($enquiry->id, 5, '0', STR_PAD_LEFT) }}</span>
                        </h4>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="request-detail-section">
        <div class="container">
            <div style="max-width:780px;margin:0 auto;">

                @if(session('payment_success'))
                <div style="padding:14px 18px;background:#d1fae5;border-radius:8px;margin-bottom:20px;font-family:var(--font-secondary);font-size:var(--fs-14);color:#065f46;display:flex;align-items:center;gap:10px;">
                    <i class="ri-checkbox-circle-fill" style="font-size:1.2rem;"></i>
                    {{ session('payment_success') }}
                </div>
                @endif

                {{-- Header --}}
                <div class="request-card" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;">
                    <div>
                        <p style="font-family:var(--font-secondary);font-size:13px;color:#888;margin-bottom:4px;">
                            Submitted {{ $enquiry->created_at->format('d M Y, h:i A') }}
                        </p>
                        <h3 style="font-family:var(--font-secondary);font-size:var(--fs-22);font-weight:var(--fw-700);color:var(--color-dk-gray);margin:0;">
                            Request #{{ str_pad($enquiry->id, 5, '0', STR_PAD_LEFT) }}
                        </h3>
                    </div>
                    <div style="text-align:right;">
                        <span class="status-badge status-{{ $enquiry->status }}">
                            {{ ucfirst(str_replace('_', ' ', $enquiry->status)) }}
                        </span>
                    </div>
                </div>

                {{-- Service Info --}}
                <div class="request-card">
                    <h4 class="request-section-title"><i class="ri-stack-line me-2" style="color:var(--color-green);"></i>Service Details</h4>
                    <div class="detail-row">
                        <span class="detail-label">Service</span>
                        <span class="detail-value">{{ $enquiry->service?->name ?? '—' }}</span>
                    </div>
                    @if($enquiry->service?->subCategory)
                    <div class="detail-row">
                        <span class="detail-label">Category</span>
                        <span class="detail-value">
                            {{ $enquiry->service->subCategory->category?->name ?? '' }}
                            @if($enquiry->service->subCategory->name)
                                &rsaquo; {{ $enquiry->service->subCategory->name }}
                            @endif
                        </span>
                    </div>
                    @endif
                </div>

                {{-- Payment State --}}
                @if($enquiry->isPaid() || $enquiry->payment_status === 'refunded')
                <div class="request-card" style="border-left:4px solid #16a34a;">
                    <h4 class="request-section-title" style="color:#16a34a;">
                        <i class="ri-checkbox-circle-line me-2"></i>Payment Confirmed
                    </h4>
                    <div class="detail-row">
                        <span class="detail-label">Amount Paid</span>
                        <span class="detail-value fw-bold">AED {{ number_format($enquiry->paid_amount, 2) }}</span>
                    </div>
                    @if($enquiry->paid_at)
                    <div class="detail-row">
                        <span class="detail-label">Paid On</span>
                        <span class="detail-value">{{ $enquiry->paid_at->format('d M Y, h:i A') }}</span>
                    </div>
                    @endif
                    @if($enquiry->payment_status === 'refunded')
                    <div class="detail-row">
                        <span class="detail-label">Refunded</span>
                        <span class="detail-value" style="color:#dc2626;">AED {{ number_format($enquiry->refunded_amount, 2) }}</span>
                    </div>
                    @endif
                    <div style="margin-top:12px;">
                        <span class="status-badge" style="background:#d1fae5;color:#065f46;">
                            <i class="ri-shield-check-line me-1"></i>
                            {{ $enquiry->payment_status === 'refunded' ? 'Refunded' : 'Paid' }}
                        </span>
                    </div>
                </div>
                @elseif($enquiry->isQuoted())
                <div class="request-card" style="border-left:4px solid var(--color-green);">
                    <h4 class="request-section-title">
                        <i class="ri-price-tag-3-line me-2" style="color:var(--color-green);"></i>Service Quote
                    </h4>
                    <div class="detail-row">
                        <span class="detail-label">Quoted Price</span>
                        <span class="detail-value fw-bold" style="font-size:1.15rem;">AED {{ number_format($enquiry->quoted_price, 2) }}</span>
                    </div>
                    <p style="font-family:var(--font-secondary);font-size:var(--fs-14);color:#666;margin:10px 0 16px;">
                        A price has been set for your service request. Click below to proceed with payment.
                    </p>
                    <form method="POST" action="{{ route('enquiries.checkout', $enquiry) }}">
                        @csrf
                        <button type="submit"
                                style="display:inline-flex;align-items:center;gap:8px;background:var(--color-green);color:#fff;border:none;padding:12px 28px;border-radius:6px;font-family:var(--font-secondary);font-size:var(--fs-14);font-weight:var(--fw-600);cursor:pointer;transition:opacity .2s;"
                                onmouseover="this.style.opacity='.88'" onmouseout="this.style.opacity='1'">
                            <i class="ri-secure-payment-line"></i>
                            Continue to Pay &mdash; AED {{ number_format($enquiry->quoted_price, 2) }}
                        </button>
                    </form>

                    @if(session('payment_success'))
                    <div style="margin-top:14px;padding:12px 16px;background:#d1fae5;border-radius:6px;font-family:var(--font-secondary);font-size:var(--fs-14);color:#065f46;">
                        <i class="ri-checkbox-circle-line me-1"></i>{{ session('payment_success') }}
                    </div>
                    @endif
                </div>
                @endif

                {{-- Contact Info --}}
                <div class="request-card">
                    <h4 class="request-section-title"><i class="ri-user-line me-2" style="color:var(--color-green);"></i>Contact Information</h4>
                    <div class="detail-row">
                        <span class="detail-label">Name</span>
                        <span class="detail-value">{{ $enquiry->full_name }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Email</span>
                        <span class="detail-value">{{ $enquiry->email }}</span>
                    </div>
                    @if($enquiry->phone)
                    <div class="detail-row">
                        <span class="detail-label">Phone</span>
                        <span class="detail-value">{{ $enquiry->phone }}</span>
                    </div>
                    @endif
                    @if($enquiry->whatsapp)
                    <div class="detail-row">
                        <span class="detail-label">WhatsApp</span>
                        <span class="detail-value">{{ $enquiry->whatsapp }}</span>
                    </div>
                    @endif
                    @if($enquiry->location)
                    <div class="detail-row">
                        <span class="detail-label">Location</span>
                        <span class="detail-value">{{ $enquiry->location }}</span>
                    </div>
                    @endif
                    @if($enquiry->language)
                    <div class="detail-row">
                        <span class="detail-label">Language</span>
                        <span class="detail-value">{{ $enquiry->language }}</span>
                    </div>
                    @endif
                    @if($enquiry->remarks)
                    <div class="detail-row">
                        <span class="detail-label">Remarks</span>
                        <span class="detail-value">{{ $enquiry->remarks }}</span>
                    </div>
                    @endif
                </div>

                <a href="{{ route('profile.show') }}?tab=requests" class="btn-back">
                    <i class="ri-arrow-left-line"></i> Back to My Requests
                </a>

            </div>
        </div>
    </section>

@endsection
