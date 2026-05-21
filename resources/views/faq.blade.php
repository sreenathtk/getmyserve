@extends('layouts.app')

@section('title', 'FAQ | Get My Serv Service Platform in Dubai')

@section('meta')
    <meta name="description" content="Find the right solutions with Get My Serv. Learn how it works and easily connect with trusted service providers in Dubai.">
    <meta name="keywords" content="Dubai services, service providers UAE, visa services Dubai, online service booking, UAE service providers guide">
    <link rel="canonical" href="{{ route('faq') }}">
    <meta property="og:locale" content="en_IN" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="Get My Serv | Everything You Need to Know" />
    <meta property="og:url" content="{{ route('faq') }}" />
    <meta property="og:site_name" content="Get My Serv" />
    <meta property="og:description" content="Get clarity on how our platform connects you with reliable service providers" />
    <meta name="robots" content="index, follow" />
@endsection

@section('content')

    {{-- Banner --}}
    <section class="about-banner ban-faq">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="about-banner-content">
                        <h2>FAQ</h2>
                        <h4><a href="{{ url('/') }}">home</a> <span>// FAQ</span></h4>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- FAQ Section --}}
    <section class="faq-section">
        <div class="container">
            <div class="row align-items-center">

                {{-- Left: Image Hexagons --}}
                <div class="col-lg-5 col-md-12 col-sm-12 col-12">
                    <div class="faq-image-wrap" data-aos="fade-right" data-aos-duration="800">
                        <div class="faq-hex-group">

                            <div class="faq-hex faq-hex-large">
                                <div class="faq-hex-inner">
                                    <img src="/assets/images/services/sv-1.jpg.jpeg" alt="FAQ Visual 1" class="img-fluid">
                                    <div class="faq-play-btn">
                                        <i class="fas fa-play"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="faq-hex faq-hex-small">
                                <div class="faq-hex-inner">
                                    <img src="/assets/images/services/sv-2.jpg.jpeg" alt="FAQ Visual 2" class="img-fluid">
                                </div>
                            </div>

                        </div>

                        <div class="faq-bg-blob"></div>
                    </div>
                </div>

                {{-- Right: FAQ Accordion --}}
                <div class="col-lg-7 col-md-12 col-sm-12 col-12">
                    <div class="faq-content-wrap" data-aos="fade-left" data-aos-duration="800">

                        <div class="faq-header">
                            <span class="faq-badge">FAQ</span>
                            <h2>Frequently Asked <span>Questions</span></h2>
                            <p>Find answers to the most common questions about our services and how we can help you.</p>
                        </div>

                        <div class="faq-header">
                            <span class="faq-badge">FAQ</span>
                            <h2>Frequently Asked <span>Questions</span></h2>
                            <p>Find answers to the most common questions about our services and how we can help you.</p>
                        </div>

                        <div class="faq-accordion" id="faqAccordion">
                            <div class="faq-item active">
                                <div class="faq-question" onclick="toggleFaq(this)">
                                    <span class="faq-num">01.</span>
                                    <span class="faq-q-text">What is GetMyServ?</span>
                                    <span class="faq-icon"><i class="fas fa-chevron-down"></i></span>
                                </div>
                                <div class="faq-answer">
                                    <p>GetMyServ is a platform that allows you to easily book a wide range of business services, including finance, tax, legal, company setup, professional services, and banking solutions—all quickly and conveniently in one place.</p>
                                </div>
                            </div>

                            <div class="faq-item">
                                <div class="faq-question" onclick="toggleFaq(this)">
                                    <span class="faq-num">02.</span>
                                    <span class="faq-q-text">How do I book a service?</span>
                                    <span class="faq-icon"><i class="fas fa-chevron-down"></i></span>
                                </div>
                                <div class="faq-answer">
                                    <p>Your journey to successful service fulfillment is simple and seamless, completed in just 6 easy steps:</p>
                                    <ol style="padding-left: 1.2em;">
                                        <li><strong>Browse Categories</strong><br><p>Explore a wide range of services such as finance, visas, residency, and more. </p></li>
                                        <li><strong>Find the Right Service</strong><br><p>Review available options and choose the service that best fits your business needs.</p></li>
                                        <li><strong>Send Request / Make Payment</strong><br><p>Fill out a quick form to connect with GetMyServ and initiate the process.</p></li>
                                        <li><strong>Get a Response</strong><br><p>Our team will contact you directly to understand your requirements and guide you further.</p></li>
                                        <li><strong>Payment & Document Submission</strong><br><p>We assist you in collecting and managing all required documents and payments.</p></li>
                                        <li><strong>Service Fulfillment</strong><br><p>GetMyServ handles the entire process end-to-end as your single point of contact.</p></li>
                                    </ol>
                                </div>
                            </div>

                            <div class="faq-item">
                                <div class="faq-question" onclick="toggleFaq(this)">
                                    <span class="faq-num">03.</span>
                                    <span class="faq-q-text">Do I need to pay in advance? What are the payment methods?</span>
                                    <span class="faq-icon"><i class="fas fa-chevron-down"></i></span>
                                </div>
                                <div class="faq-answer">
                                    <p>Payment requirements depend on the service selected. Once you choose a service, the GetMyServ team will contact you to explain the details, including pricing and payment terms.<br>We offer convenient payment options such as:</p>
                                    <ul style="padding-left: 1.2em;">
                                        <li>Bank transfer</li>
                                        <li>Card payment</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="faq-item">
                                <div class="faq-question" onclick="toggleFaq(this)">
                                    <span class="faq-num">04.</span>
                                    <span class="faq-q-text">How can I contact support or raise a complaint?</span>
                                    <span class="faq-icon"><i class="fas fa-chevron-down"></i></span>
                                </div>
                                <div class="faq-answer">
                                    <p>We are here to help you. You can reach our support team through the following channels:</p>
                                    <ul style="padding-left: 1.2em;">
                                        <li>📞 <strong>Call Us:</strong><br>+971 56 522 0202</li>
                                        <li>📧 <strong>Email:</strong><br>info@getmyserv.com</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
    </section>

@endsection

@push('scripts')
<script>
    function toggleFaq(el) {
        var item = el.closest('.faq-item');
        document.querySelectorAll('.faq-item').forEach(function (i) {
            if (i !== item) i.classList.remove('active');
        });
        item.classList.toggle('active');
    }
</script>
@endpush
