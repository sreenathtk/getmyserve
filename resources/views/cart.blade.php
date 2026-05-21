@extends('layouts.app')

@section('title', 'Cart | Get My Serv')

@push('styles')
<style>
    /* ---- cart section ---- */
    .cart-section {
        padding: 60px 0;
        background-color: var(--bg-color);
    }
    .table-bg {
        background-color: var(--color-white);
        padding: 40px;
    }
    .table-scroll {
        overflow-x: auto;
    }
    .table {
        border-bottom: 1px solid var(--color-dk-gray) !important;
        margin: 0 0 40px 0;
    }
    .table tr th {
        background-color: var(--color-green);
        font-family: var(--font-secondary);
        font-size: var(--fs-16);
        font-weight: var(--fw-600);
        color: var(--color-white);
        text-align: center;
    }
    .table tr td {
        font-family: var(--font-secondary);
        font-size: var(--fs-16);
        font-weight: var(--fw-600);
        text-align: center;
        vertical-align: middle;
        border-bottom: 1px solid var(--border-color) !important;
    }
    .cart-summary {
        background-color: var(--bg-color);
        padding: 10px 5px;
    }
    .cart-head h1 {
        font-family: var(--font-secondary);
        font-size: var(--fs-20);
        font-weight: var(--fw-600);
        color: var(--color-dk-gray);
        margin: 0 0 20px 0;
        text-decoration: underline;
    }
    .cart-details {
        border-bottom: 1px solid var(--color-dk-gray);
        margin: 0 0 12px 0;
    }
    .cart-details li {
        font-family: var(--font-secondary);
        font-size: var(--fs-16);
        font-weight: var(--fw-600);
        color: var(--color-dk-gray);
        margin: 0 0 8px 0;
    }
    .cart-details span { float: right; }
    .grand-summary h1 {
        font-family: var(--font-secondary);
        font-size: var(--fs-18);
        font-weight: var(--fw-600);
        color: var(--color-dk-gray);
    }
    .grand-summary h1 span { float: right; }
    .remove {
        background: none;
        border: none;
        cursor: pointer;
        padding: 0;
    }
    .pkg-badge {
        font-size: 12px;
        background-color: var(--font-shade);
        color: var(--color-dk-gray);
        padding: 3px 10px;
        border-radius: 5px;
        display: inline-block;
    }
    .svc-placeholder {
        width: 80px;
        height: 60px;
        background: linear-gradient(135deg, var(--color-green), #3a7bd5);
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
    }
    .svc-placeholder i { color: #fff; font-size: 1.4rem; }
    .cart-empty {
        text-align: center;
        padding: 60px 20px;
        color: var(--color-dk-gray);
    }
    .cart-empty i { font-size: 4rem; opacity: .2; display: block; margin-bottom: 16px; }
    .cart-actions {
        margin-top: 16px;
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    .btn-clear-cart {
        font-size: 14px;
        color: #dc3545;
        background: none;
        border: 1px solid #dc3545;
        padding: 6px 16px;
        border-radius: 5px;
        cursor: pointer;
        transition: all .2s;
    }
    .btn-clear-cart:hover { background: #dc3545; color: #fff; }
    .checkout-box {
        background-color: var(--bg-color);
        padding: 24px;
        border-radius: 8px;
    }
    .checkout-box h2 {
        font-family: var(--font-secondary);
        font-size: var(--fs-18);
        font-weight: var(--fw-600);
        color: var(--color-dk-gray);
        margin: 0 0 16px 0;
        text-decoration: underline;
    }
    .checkout-total {
        display: flex;
        justify-content: space-between;
        font-family: var(--font-secondary);
        font-size: var(--fs-18);
        font-weight: var(--fw-700);
        color: var(--color-dk-gray);
        margin-bottom: 20px;
    }
    .checkout-total .amount { color: var(--color-green); }
    .btn-checkout {
        display: block;
        width: 100%;
        background-color: var(--color-green);
        color: var(--color-white);
        font-family: var(--font-secondary);
        font-size: var(--fs-16);
        font-weight: var(--fw-600);
        padding: 13px 20px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        text-align: center;
        text-decoration: none;
        transition: opacity .2s;
    }
    .btn-checkout:hover { opacity: .85; color: var(--color-white); }
    .secure-badge {
        font-size: .78rem;
        color: #999;
        text-align: center;
        margin-top: 10px;
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
                        <h2>Cart</h2>
                        <h4>
                            <a href="{{ url('/') }}">home</a>
                            <span>// Cart</span>
                        </h4>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Cart --}}
    <section class="cart-section">
        <div class="container">

            @if(session('cart_success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i>{{ session('cart_success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('cart_error'))
                <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i>{{ session('cart_error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row">
                <div class="col-12">
                    <div class="table-bg">

                        @if($cartItems->isEmpty())

                            <div class="cart-empty">
                                <i class="fa-solid fa-cart-shopping"></i>
                                <p style="font-size:1.1rem; margin-bottom:8px;">Your cart is empty</p>
                                <p style="font-size:.9rem; opacity:.6; margin-bottom:20px;">Browse our services and add something you like.</p>
                                <a href="{{ route('services.index') }}" class="buttn"><span style="font-size:14px; color:var(--color-white); background-color:var(--color-green); padding:8px 20px; display:inline-block; border-radius:5px; font-family:var(--font-secondary);">Browse Services <i class="fa fa-arrow-right ms-2"></i></span></a>
                            </div>

                        @else

                            <div class="table-scroll">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Image</th>
                                            <th>Service</th>
                                            <th>Price</th>
                                            <th>Total</th>
                                            <th>Remove</th>
                                        </tr>
                                    </thead>

                                    @foreach($cartItems as $item)
                                    <tbody class="body-details">
                                        <tr>
                                            <td>
                                                @if($item->image)
                                                    <img src="{{ asset('storage/' . $item->image) }}"
                                                         width="80" alt="{{ $item->service_name }}"
                                                         style="border-radius:6px; object-fit:cover; height:60px;">
                                                @else
                                                    <div class="svc-placeholder">
                                                        <i class="fa-solid fa-briefcase"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $item->service_name }}
                                                @if($item->package !== 'standard')
                                                    <br><span class="pkg-badge">{{ ucfirst($item->package) }} Package</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($item->price)
                                                    AED {{ number_format($item->price, 0) }}
                                                @else
                                                    <span style="font-size:.85rem; opacity:.6;">On Enquiry</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($item->price)
                                                    AED {{ number_format($item->price, 0) }}
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            <td>
                                                <form method="POST" action="{{ route('cart.remove', $item->id) }}" class="m-0">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="remove"
                                                        onclick="return confirm('Remove this item?')">
                                                        <i class="fa fa-trash" style="color: rgb(3, 168, 78);"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    </tbody>
                                    @endforeach

                                </table>
                            </div>

                            @php
                                $subtotal      = $cartItems->sum('price');
                                $hasUnpriced   = $cartItems->contains(fn($i) => is_null($i->price));
                                $totalDiscount = 0;
                                $appliedOffers = [];
                                foreach ($comboOffers as $offerId => $comboOffer) {
                                    $groupSum = $cartItems->where('combo_offer_id', $offerId)->sum('price');
                                    $discount = $groupSum - $comboOffer->offer_price;
                                    if ($discount > 0) {
                                        $totalDiscount  += $discount;
                                        $appliedOffers[] = ['title' => $comboOffer->title, 'discount' => $discount];
                                    }
                                }
                                $grandTotal = $subtotal - $totalDiscount;
                                $vatAmount  = $vatRate > 0 ? round($grandTotal * $vatRate / 100, 2) : 0;
                                $finalTotal = $grandTotal + $vatAmount;
                            @endphp

                            <div class="row gy-4 align-items-start">

                                {{-- Cart Summary (left) --}}
                                <div class="col-lg-5 col-md-12 col-sm-12 col-12">
                                    <div class="cart-summary">
                                        <div class="cart-head">
                                            <h1>Cart Summary</h1>
                                        </div>
                                        <div class="cart-details">
                                            <ul>
                                                <li>
                                                    Sub Total
                                                    <span>
                                                        @if($subtotal > 0) AED {{ number_format($subtotal, 0) }}
                                                        @else —
                                                        @endif
                                                    </span>
                                                </li>
                                                @foreach($appliedOffers as $applied)
                                                <li style="color: var(--color-green);">
                                                    <i class="fa fa-tag me-1"></i> Offer Applied: {{ $applied['title'] }}
                                                    <span>− AED {{ number_format($applied['discount'], 0) }}</span>
                                                </li>
                                                @endforeach
                                            </ul>
                                            @if($hasUnpriced)
                                                <p style="font-size:.8rem; opacity:.65; margin-bottom:8px;">
                                                    <i class="fa fa-info-circle me-1"></i>
                                                    Some items are priced on enquiry and excluded from the total.
                                                </p>
                                            @endif
                                        </div>
                                        @if($vatRate > 0 && $grandTotal > 0)
                                        <div class="cart-details" style="border-bottom:none;margin-bottom:0;">
                                            <ul>
                                                <li>
                                                    After Discount Total
                                                    <span>AED {{ number_format($grandTotal, 0) }}</span>
                                                </li>
                                                <li style="color:var(--color-dk-gray);">
                                                    VAT ({{ $vatRate }}%)
                                                    <span>+ AED {{ number_format($vatAmount, 2) }}</span>
                                                </li>
                                            </ul>
                                        </div>
                                        @endif
                                        <div class="grand-summary">
                                            <h1>
                                                Grand Total
                                                <span>
                                                    @if($finalTotal > 0) AED {{ number_format($finalTotal, 2) }}
                                                    @else —
                                                    @endif
                                                </span>
                                            </h1>
                                        </div>
                                    </div>
                                </div>

                                {{-- Checkout Box (right) --}}
                                <div class="col-lg-4 offset-lg-3 col-md-12 col-sm-12 col-12">
                                    <div class="checkout-box">
                                        <h2>Checkout</h2>

                                        @if($finalTotal > 0)
                                            @if($vatRate > 0 && $vatAmount > 0)
                                            <div class="checkout-total" style="font-size:.88rem;margin-bottom:4px;opacity:.7;">
                                                <span>Subtotal after discounts</span>
                                                <span>AED {{ number_format($grandTotal, 2) }}</span>
                                            </div>
                                            <div class="checkout-total" style="font-size:.88rem;margin-bottom:12px;opacity:.7;">
                                                <span>VAT ({{ $vatRate }}%)</span>
                                                <span>AED {{ number_format($vatAmount, 2) }}</span>
                                            </div>
                                            @endif
                                            <div class="checkout-total">
                                                <span>Total</span>
                                                <span class="amount">AED {{ number_format($finalTotal, 2) }}</span>
                                            </div>

                                            @auth
                                                <form method="POST" action="{{ route('checkout.session') }}">
                                                    @csrf
                                                    <button type="submit" class="btn-checkout">
                                                        <i class="fa-solid fa-lock me-2"></i> Proceed to Pay
                                                    </button>
                                                </form>
                                            @else
                                                <a href="{{ route('login') }}" class="btn-checkout">
                                                    <i class="fa-solid fa-lock me-2"></i> Login to Checkout
                                                </a>
                                            @endauth

                                            <p class="secure-badge">
                                                <i class="fa-solid fa-shield-halved me-1"></i> Secured by Stripe
                                            </p>
                                        @else
                                            <p style="font-family:var(--font-secondary); font-size:.88rem; color:#888; text-align:center; margin:0;">
                                                All items are priced on enquiry.<br>Online payment is not available for these services.
                                            </p>
                                        @endif
                                    </div>
                                </div>

                            </div>

                            <div class="cart-actions mt-4">
                                <a href="{{ route('services.index') }}" class="buttn">
                                    <span style="font-size:14px; color:var(--color-white); background-color:var(--color-green); padding:8px 20px; display:inline-block; border-radius:5px; font-family:var(--font-secondary);">
                                        <i class="fa-solid fa-plus me-1"></i> Add More Services
                                    </span>
                                </a>
                                <form method="POST" action="{{ route('cart.clear') }}" class="m-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-clear-cart"
                                        onclick="return confirm('Clear all items from cart?')">
                                        <i class="fa fa-trash me-1"></i> Clear Cart
                                    </button>
                                </form>
                            </div>

                        @endif

                    </div>
                </div>
            </div>

        </div>
    </section>

@endsection
