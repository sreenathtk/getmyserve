<footer>
    <section class="footer-top">
        <div class="container">
            <div class="row">
                <div class="col-lg-4">
                    <div class="footer-wrapper">
                        <div class="footer-wrapper-header"><h2>contact info</h2></div>
                        <div class="footer-wrapper-content">
                            <ul>
                                @if(!empty($settings['address']))
                                    <li><i class="far fa-map-marker-alt"></i><span>{{ $settings['address'] }}</span></li>
                                @endif
                                @if(!empty($settings['email']))
                                    <li><i class="far fa-envelope"></i><span><a href="mailto:{{ $settings['email'] }}" class="mail-link">{{ $settings['email'] }}</a></span></li>
                                @endif
                                @if(!empty($settings['phone']))
                                    <li><span class="footer-phone"><i class="far fa-phone-volume"></i><a href="tel:{{ $settings['phone'] }}" class="tel-link">{{ $settings['phone'] }}</a></span></li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="footer-wrapper">
                        <div class="footer-wrapper-header"><h2>quick links</h2></div>
                        <div class="footer-wrapper-content-list">
                            <ul>
                                <li><a href="{{ url('/') }}">home</a></li>
                                <li><a href="{{ url('/about') }}">about</a></li>
                                <li><a href="{{ route('categories.index') }}">service</a></li>
                                 <li><a href="{{ url('/contact') }}">contact us</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="footer-wrapper">
                        <div class="footer-wrapper-header"><h2>featured services</h2></div>
                        <div class="footer-wrapper-content-list">
                            <ul>
                                <li><a href="{{ route('faq') }}">Frequently asked questions</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="footer-wrapper">
                        <div class="footer-wrapper-header"><h2>whatsapp channel subscription</h2></div>
                        <div class="footer-wrapper-content">
                            <form action="">
                                <div class="footer-email">
                                    <input type="email" placeholder="Enter Your Number">
                                    <button type="submit" class="footer-btn">subscribe</button>
                                </div>
                                <a href="{{ route('provider-registration.create') }}" class="footer-partner">Register Your Company</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</footer>

<a href="https://wa.me/{{ preg_replace('/\D/', '', $settings['phone'] ?? '') }}"
    class="whatsapp-float"
    target="_blank"
    data-bs-toggle="tooltip"
    data-bs-placement="left"
    title="Chat with us on WhatsApp"
    data-aos="fade-up">
    <img src="/assets/images/Logo/whatsapp.png" alt="WhatsApp" />
</a>

<!-- Mobile Footer -->
<section class="mobile-footer">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="mobile-footer-wrapper">
                    <div class="mobile-footer-wrapper-content">
                        <ul>
                            <li><a href="{{ url('/') }}"><i class="far fa-home"></i><span>Home</span></a></li>
                            <li><a href="{{ route('categories.index') }}"><i class="far fa-search"></i><span>Search</span></a></li>
                            @auth
                                <li><a href="{{ route('profile.show') }}"><i class="far fa-user"></i><span>Profile</span></a></li>
                                <li><a href="{{ route('orders.index') }}"><i class="fa-solid fa-box"></i><span>Orders</span></a></li>
                            @else
                                <li><a href="{{ route('login') }}"><i class="far fa-user"></i><span>Profile</span></a></li>
                                <li><a href="{{ route('login') }}"><i class="fa-solid fa-box"></i><span>Orders</span></a></li>
                            @endauth
                            <li><a href="{{ route('assistance-request.create') }}"><i class="far fa-sticky-note"></i><span>Enquiry</span></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
