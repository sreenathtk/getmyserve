@php
    $headerCartCount = auth()->check()
        ? \App\Models\CartItem::where('user_id', auth()->id())->count()
        : \App\Models\CartItem::where('session_id', session()->getId())->count();
@endphp

<header>
    <section class="Getmyservice-header sticky-nav d-none d-lg-block">
        <div class="container">
            <div class="row align-items-center justify-content-between">
                <div class="col-auto">
                    <div class="get-my-header-logo">
                        <a href="{{ url('/') }}"><img src="/assets/images/Logo/LOGO.png" alt="" class="img-fluid"></a>
                    </div>
                </div>
                <div class="col-auto d-none d-lg-block">
                    <div class="get-my-header-nav">
                        <ul>
                            <li><a href="{{ url('/') }}">home</a></li>
                            <li><a href="{{ url('/about') }}">about</a></li>
                            <li><a href="{{ route('categories.index') }}">services</a></li>
                            <li><a href="{{ url('/contact') }}">contact us</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="get-my-header-reg">
                        <a href="{{ route('cart.index') }}" style="position:relative;">
                            <i class="fas fa-shopping-cart"></i>Cart
                            @if($headerCartCount > 0)
                                <span style="position:absolute;top:-6px;right:-10px;background:var(--color-green);color:#fff;font-size:10px;font-weight:700;border-radius:50%;width:18px;height:18px;display:flex;align-items:center;justify-content:center;line-height:1;">{{ $headerCartCount }}</span>
                            @endif
                        </a>
                        <a href="{{ route('assistance-request.create') }}"><i class="far fa-sticky-note"></i>Enquiry</a>
                        @auth
                            <a href="{{ route('profile.show') }}"><i class="far fa-user me-1"></i>Profile</a>
                            <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit"><i class="far fa-sign-out me-1"></i>Logout</button>
                            </form>
                        @else
                            <a href="{{ route('login') }}"><i class="far fa-user"></i>Login</a>
                        @endauth
                    </div>
                </div>
                <div class="col-auto d-block d-lg-none">
                    <div class="crystal-hamburger"><i class="fa fa-bars" aria-hidden="true"></i></div>
                </div>
            </div>
        </div>
    </section>
</header>

<!-- Mobile Header -->
<div class="mobile-header sticky-nav d-flex d-lg-none">
    <div class="mobile-logo">
        <a href="{{ url('/') }}"><img src="/assets/images/Logo/LOGO.png" alt="" class="img-fluid"></a>
    </div>
    <div class="mobile-menu-toggle mob-menus">
        <div class="col-auto">
            <div class="get-my-header-reg">
                <a href="{{ route('cart.index') }}" style="position:relative;">
                    <i class="fas fa-shopping-cart"></i>
                    @if($headerCartCount > 0)
                        <span style="position:absolute;top:-6px;right:-10px;background:var(--color-green);color:#fff;font-size:10px;font-weight:700;border-radius:50%;width:18px;height:18px;display:flex;align-items:center;justify-content:center;line-height:1;">{{ $headerCartCount }}</span>
                    @endif
                </a>
            </div>
        </div>
        <ion-icon name="list-outline" id="menu-open" class="icon"></ion-icon>
    </div>
</div>

<nav class="mobile-menu" id="mobile-menu">
    <div class="mobile-menu-top">
        <div class="mobile-logo">
            <a href="{{ url('/') }}"><img src="/assets/images/Logo/LOGO.png" alt="" class="img-fluid"></a>
        </div>
        <div class="mobile-menu-close" id="menu-close">&times;</div>
    </div>
    <ul class="mob-menu">
        <li><a href="{{ url('/') }}">Home</a></li>
        <li><a href="{{ url('/about') }}">About us</a></li>
        <li><a href="{{ route('categories.index') }}">Services</a></li>
        <li><a href="{{ route('faq') }}">FAQ</a></li>
        <li><a href="{{ url('/contact') }}">Contact</a></li>
        <li><a href="{{ route('assistance-request.create') }}">Enquiry</a></li>
        @auth
            <li><a href="{{ route('profile.show') }}"><i class="far fa-user me-1"></i>My Profile</a></li>
            <li><a href="{{ route('orders.index') }}"><i class="fa-solid fa-box me-1"></i>My Orders</a></li>
            <li>
                <a href="#" onclick="event.preventDefault();document.getElementById('mob-logout-form').submit();">
                    <i class="far fa-sign-out me-1"></i>Logout
                </a>
            </li>
        @else
            <li><a href="{{ route('login') }}"><i class="far fa-user"></i>Login</a></li>
        @endauth
    </ul>
    @auth
        <form id="mob-logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
            @csrf
        </form>
    @endauth
</nav>
