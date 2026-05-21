@extends('layouts.app')

@section('title', 'My Profile | Get My Serv')

@push('styles')
<style>
    .profile-section {
        padding: 60px 0;
        background-color: var(--bg-color);
    }
    .profile-card {
        background-color: var(--color-white);
        padding: 36px 40px;
    }
    .profile-tabs {
        display: flex;
        gap: 0;
        border-bottom: 2px solid var(--border-color);
        margin-bottom: 32px;
    }
    .profile-tab-btn {
        background: none;
        border: none;
        padding: 12px 28px;
        font-family: var(--font-secondary);
        font-size: var(--fs-15);
        font-weight: var(--fw-600);
        color: var(--color-dk-gray);
        cursor: pointer;
        border-bottom: 2px solid transparent;
        margin-bottom: -2px;
        transition: color .2s, border-color .2s;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .profile-tab-btn.active {
        color: var(--color-green);
        border-bottom-color: var(--color-green);
    }
    .profile-tab-btn:hover:not(.active) {
        color: var(--color-green);
    }
    .profile-tab-pane { display: none; }
    .profile-tab-pane.active { display: block; }

    .profile-section-title {
        font-family: var(--font-secondary);
        font-size: var(--fs-20);
        font-weight: var(--fw-700);
        color: var(--color-dk-gray);
        margin-bottom: 24px;
    }
    .profile-form .form-label {
        font-family: var(--font-secondary);
        font-size: var(--fs-14);
        font-weight: var(--fw-600);
        color: var(--color-dk-gray);
        margin-bottom: 6px;
    }
    .profile-form .form-control {
        font-family: var(--font-secondary);
        font-size: var(--fs-14);
        border: 1px solid var(--border-color);
        border-radius: 6px;
        padding: 10px 14px;
        color: var(--color-dk-gray);
        background-color: var(--color-white);
        transition: border-color .2s;
    }
    .profile-form .form-control:focus {
        border-color: var(--color-green);
        box-shadow: 0 0 0 3px rgba(var(--color-green-rgb, 34,197,94), .12);
        outline: none;
    }
    .profile-form .form-control.is-invalid { border-color: #dc3545; }
    .profile-save-btn {
        background-color: var(--color-green);
        color: var(--color-white);
        border: none;
        padding: 11px 32px;
        border-radius: 6px;
        font-family: var(--font-secondary);
        font-size: var(--fs-15);
        font-weight: var(--fw-600);
        cursor: pointer;
        transition: opacity .2s;
    }
    .profile-save-btn:hover { opacity: .88; }

    /* Orders tab */
    .table tr th {
        background-color: var(--color-green);
        font-family: var(--font-secondary);
        font-size: var(--fs-15);
        font-weight: var(--fw-600);
        color: var(--color-white);
    }
    .table tr td {
        font-family: var(--font-secondary);
        font-size: var(--fs-15);
        font-weight: var(--fw-500);
        vertical-align: middle;
        border-bottom: 1px solid var(--border-color) !important;
    }
    .status-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: .82rem;
        font-weight: 600;
        font-family: var(--font-secondary);
    }
    .status-pending    { background:#fff3cd; color:#856404; }
    .status-paid       { background:#d1e7dd; color:#0f5132; }
    .status-processing { background:#cff4fc; color:#055160; }
    .status-completed  { background:#d1fae5; color:#065f46; }
    .status-cancelled  { background:#f8d7da; color:#842029; }
    .status-failed     { background:#f8d7da; color:#842029; }
    .refund-requested  { background:#fff3cd; color:#856404; }
    .refund-partial    { background:#cff4fc; color:#055160; }
    .refund-full       { background:#f8d7da; color:#842029; }
    .btn-view-order {
        background-color: var(--color-green);
        color: var(--color-white);
        padding: 6px 16px;
        border-radius: 5px;
        font-family: var(--font-secondary);
        font-size: .85rem;
        font-weight: 600;
        text-decoration: none;
        display: inline-block;
        transition: opacity .2s;
    }
    .btn-view-order:hover { opacity: .85; color: var(--color-white); }
    .btn-all-orders {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background-color: var(--color-green);
        color: var(--color-white);
        padding: 10px 24px;
        border-radius: 6px;
        font-family: var(--font-secondary);
        font-size: var(--fs-14);
        font-weight: var(--fw-600);
        text-decoration: none;
        transition: opacity .2s;
        margin-top: 20px;
    }
    .btn-all-orders:hover { opacity: .88; color: var(--color-white); }
    .orders-empty {
        text-align: center;
        padding: 50px 20px;
        color: var(--color-dk-gray);
    }
    .orders-empty i { font-size: 3.5rem; opacity: .2; display: block; margin-bottom: 14px; }

    @media (max-width: 576px) {
        .profile-card { padding: 24px 16px; }
        .profile-tab-btn { padding: 10px 14px; font-size: 13px; }
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
                        <h2>My Profile</h2>
                        <h4>
                            <a href="{{ url('/') }}">home</a>
                            <span>// My Profile</span>
                        </h4>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="profile-section">
        <div class="container">

            @if(session('profile_success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i>{{ session('profile_success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('notifications_success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <i class="ri-whatsapp-line me-2" style="color:#25d366;"></i>{{ session('notifications_success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('password_success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i>{{ session('password_success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="profile-card">

                {{-- Tabs --}}
                <div class="profile-tabs" role="tablist">
                    <button class="profile-tab-btn {{ session('tab') !== 'password' && session('tab') !== 'orders' ? 'active' : '' }}"
                            data-tab="profile" role="tab">
                        <i class="far fa-user"></i> Profile
                    </button>
                    <button class="profile-tab-btn {{ session('tab') === 'password' ? 'active' : '' }}"
                            data-tab="password" role="tab">
                        <i class="far fa-lock"></i> Password
                    </button>
                    <button class="profile-tab-btn {{ session('tab') === 'orders' ? 'active' : '' }}"
                            data-tab="orders" role="tab">
                        <i class="fa-solid fa-box"></i> My Orders
                    </button>
                </div>

                {{-- Profile Tab --}}
                <div class="profile-tab-pane {{ session('tab') !== 'password' && session('tab') !== 'orders' ? 'active' : '' }}"
                     id="tab-profile">
                    <h2 class="profile-section-title">Personal Information</h2>
                    <form method="POST" action="{{ route('profile.update') }}" class="profile-form">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name"
                                       class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone"
                                       class="form-control @error('phone') is-invalid @enderror"
                                       value="{{ old('phone', $user->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="ri-whatsapp-line" style="color:#25d366;"></i> WhatsApp Number
                                </label>
                                <input type="text" name="whatsapp_number"
                                       class="form-control @error('whatsapp_number') is-invalid @enderror"
                                       placeholder="e.g. 971501234567"
                                       value="{{ old('whatsapp_number', $user->whatsapp_number) }}">
                                @error('whatsapp_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Country</label>
                                <input type="text" name="country"
                                       class="form-control @error('country') is-invalid @enderror"
                                       value="{{ old('country', $user->country) }}">
                                @error('country')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">City</label>
                                <input type="text" name="city"
                                       class="form-control @error('city') is-invalid @enderror"
                                       value="{{ old('city', $user->city) }}">
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Address</label>
                                <input type="text" name="address"
                                       class="form-control @error('address') is-invalid @enderror"
                                       value="{{ old('address', $user->address) }}">
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 mt-2">
                                <div class="p-3 rounded" style="background:#f8fff9;border:1px solid #c3e6cb;">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input" type="checkbox"
                                                   role="switch"
                                                   id="whatsapp_notifications"
                                                   name="whatsapp_notifications"
                                                   value="1"
                                                   {{ old('whatsapp_notifications', $user->whatsapp_notifications ?? true) ? 'checked' : '' }}>
                                        </div>
                                        <div>
                                            <label class="form-label mb-0" for="whatsapp_notifications" style="cursor:pointer;">
                                                <i class="ri-whatsapp-line me-1" style="color:#25d366;font-size:1.1rem;"></i>
                                                <strong>WhatsApp Notifications</strong>
                                            </label>
                                            <p class="mb-0 text-muted" style="font-size:12px;">
                                                Receive order and service updates on your WhatsApp number.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 mt-2">
                                <button type="submit" class="profile-save-btn">
                                    <i class="fa fa-save me-2"></i>Save Changes
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Password Tab --}}
                <div class="profile-tab-pane {{ session('tab') === 'password' ? 'active' : '' }}"
                     id="tab-password">
                    <h2 class="profile-section-title">Change Password</h2>
                    <form method="POST" action="{{ route('profile.password') }}" class="profile-form">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Current Password <span class="text-danger">*</span></label>
                                <input type="password" name="current_password"
                                       class="form-control @error('current_password') is-invalid @enderror"
                                       autocomplete="current-password" required>
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6"></div>
                            <div class="col-md-6">
                                <label class="form-label">New Password <span class="text-danger">*</span></label>
                                <input type="password" name="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       autocomplete="new-password" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                                <input type="password" name="password_confirmation"
                                       class="form-control" autocomplete="new-password" required>
                            </div>
                            <div class="col-12 mt-2">
                                <button type="submit" class="profile-save-btn">
                                    <i class="fa fa-lock me-2"></i>Update Password
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Orders Tab --}}
                <div class="profile-tab-pane {{ session('tab') === 'orders' ? 'active' : '' }}"
                     id="tab-orders">
                    <h2 class="profile-section-title">Recent Orders</h2>

                    @if($orders->isEmpty())
                        <div class="orders-empty">
                            <i class="fa-solid fa-box-open"></i>
                            <p style="font-size:1.1rem;margin-bottom:8px;">No orders yet</p>
                            <p style="font-size:.9rem;opacity:.6;margin-bottom:20px;">
                                Once you complete a purchase, your orders will appear here.
                            </p>
                            <a href="{{ route('services.index') }}" class="btn-view-order">
                                Browse Services <i class="fa fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Order #</th>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                    <tr>
                                        <td><strong>#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</strong></td>
                                        <td>{{ $order->created_at->format('d M Y') }}</td>
                                        <td><strong>AED {{ number_format($order->amount, 2) }}</strong></td>
                                        <td>
                                            <span class="status-badge status-{{ $order->status }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('orders.show', $order) }}" class="btn-view-order">
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <a href="{{ route('orders.index') }}" class="btn-all-orders">
                            <i class="fa-solid fa-box"></i> View All Orders
                        </a>
                    @endif
                </div>

            </div>
        </div>
    </section>

@endsection

@push('scripts')
<script>
(function () {
    const btns  = document.querySelectorAll('.profile-tab-btn');
    const panes = document.querySelectorAll('.profile-tab-pane');

    btns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            const target = this.dataset.tab;
            btns.forEach(function (b) { b.classList.remove('active'); });
            panes.forEach(function (p) { p.classList.remove('active'); });
            this.classList.add('active');
            document.getElementById('tab-' + target).classList.add('active');
        });
    });
}());
</script>
@endpush
