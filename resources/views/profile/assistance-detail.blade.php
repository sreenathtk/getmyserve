@extends('layouts.app')

@section('title', 'Assistance Request #' . str_pad($assistanceRequest->id, 5, '0', STR_PAD_LEFT) . ' | Get My Serv')

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
    .staff-note-box {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 8px;
        padding: 16px 20px;
        font-family: var(--font-secondary);
        font-size: var(--fs-14);
        color: var(--color-dk-gray);
        line-height: 1.6;
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
                        <h2>Assistance Request</h2>
                        <h4>
                            <a href="{{ url('/') }}">Home</a>
                            <span>// </span>
                            <a href="{{ route('profile.show') }}">My Profile</a>
                            <span>// #{{ str_pad($assistanceRequest->id, 5, '0', STR_PAD_LEFT) }}</span>
                        </h4>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="request-detail-section">
        <div class="container">
            <div style="max-width:780px;margin:0 auto;">

                {{-- Header --}}
                <div class="request-card" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;">
                    <div>
                        <p style="font-family:var(--font-secondary);font-size:13px;color:#888;margin-bottom:4px;">
                            Submitted {{ $assistanceRequest->created_at->format('d M Y, h:i A') }}
                        </p>
                        <h3 style="font-family:var(--font-secondary);font-size:var(--fs-22);font-weight:var(--fw-700);color:var(--color-dk-gray);margin:0;">
                            Assistance #{{ str_pad($assistanceRequest->id, 5, '0', STR_PAD_LEFT) }}
                        </h3>
                    </div>
                    <div>
                        <span class="status-badge status-{{ $assistanceRequest->status }}">
                            {{ ucfirst(str_replace('_', ' ', $assistanceRequest->status)) }}
                        </span>
                    </div>
                </div>

                {{-- Service Info --}}
                <div class="request-card">
                    <h4 class="request-section-title"><i class="ri-customer-service-2-line me-2" style="color:var(--color-green);"></i>Request Details</h4>
                    <div class="detail-row">
                        <span class="detail-label">Service</span>
                        <span class="detail-value">{{ $assistanceRequest->service?->name ?? 'General Assistance' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Status</span>
                        <span class="detail-value">
                            <span class="status-badge status-{{ $assistanceRequest->status }}" style="padding:3px 12px;font-size:13px;">
                                {{ ucfirst(str_replace('_', ' ', $assistanceRequest->status)) }}
                            </span>
                        </span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Submitted</span>
                        <span class="detail-value">{{ $assistanceRequest->created_at->format('d M Y, h:i A') }}</span>
                    </div>
                </div>

                {{-- Contact Info --}}
                <div class="request-card">
                    <h4 class="request-section-title"><i class="ri-user-line me-2" style="color:var(--color-green);"></i>Contact Information</h4>
                    <div class="detail-row">
                        <span class="detail-label">Name</span>
                        <span class="detail-value">{{ $assistanceRequest->full_name }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Email</span>
                        <span class="detail-value">{{ $assistanceRequest->email }}</span>
                    </div>
                    @if($assistanceRequest->phone)
                    <div class="detail-row">
                        <span class="detail-label">Phone</span>
                        <span class="detail-value">{{ $assistanceRequest->phone }}</span>
                    </div>
                    @endif
                    @if($assistanceRequest->whatsapp)
                    <div class="detail-row">
                        <span class="detail-label">WhatsApp</span>
                        <span class="detail-value">{{ $assistanceRequest->whatsapp }}</span>
                    </div>
                    @endif
                    @if($assistanceRequest->location)
                    <div class="detail-row">
                        <span class="detail-label">Location</span>
                        <span class="detail-value">{{ $assistanceRequest->location }}</span>
                    </div>
                    @endif
                    @if($assistanceRequest->language)
                    <div class="detail-row">
                        <span class="detail-label">Language</span>
                        <span class="detail-value">{{ $assistanceRequest->language }}</span>
                    </div>
                    @endif
                    @if($assistanceRequest->remarks)
                    <div class="detail-row">
                        <span class="detail-label">Remarks</span>
                        <span class="detail-value">{{ $assistanceRequest->remarks }}</span>
                    </div>
                    @endif
                </div>

                {{-- Staff Notes (visible once assigned / in progress) --}}
                @if($assistanceRequest->staff_notes && $assistanceRequest->status !== 'pending')
                <div class="request-card">
                    <h4 class="request-section-title"><i class="ri-sticky-note-line me-2" style="color:var(--color-green);"></i>Notes from Our Team</h4>
                    <div class="staff-note-box">
                        {{ $assistanceRequest->staff_notes }}
                    </div>
                </div>
                @endif

                <a href="{{ route('profile.show') }}?tab=assistance" class="btn-back">
                    <i class="ri-arrow-left-line"></i> Back to My Assistance Requests
                </a>

            </div>
        </div>
    </section>

@endsection
