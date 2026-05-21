@extends('layouts.app')

@section('title', 'Contact Get My Serv | Get Support & Service Assistance in Dubai')

@section('meta')
    <meta name="description"
        content="Need help? Contact Get My Serv to connect with trusted service providers or get assistance with your service requirements.">
    <meta name="keywords" content="Dubai services, service providers UAE, visa services Dubai, online service booking, Dubai service support, UAE service help, connect service providers Dubai">
    <link rel="canonical" href="#">
    <meta property="og:locale" content="en_IN" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="Contact us | We're Here to Help" />
    <meta property="og:url" content="PAGE_URL" />
    <meta property="og:site_name" content="Drunken Duck" />
    <meta property="og:image" content="#" />
    <meta property="og:image:width" content="1200" />
    <meta property="og:image:height" content="550" />
    <meta property="og:description"
        content="Reach out to our team for support and service connections in Dubai." />
    <meta name="robots" content="index, follow" />
@endsection

@section('content')

    {{-- Contact Banner --}}
    <section class="about-banner ban-contact">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="about-banner-content">
                        <h2>contact Us</h2>
                        <h4><a href="{{ url('/') }}">home</a> <span>// Contact</span></h4>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Contact Info Section --}}
    <section class="contact-info-section">
        <div class="container">
            <div class="row contact-info-wrapper align-items-center">

                {{-- Call --}}
                <div class="col-lg-3 col-md-6 col-sm-12">
                    <div class="contact-box">
                        <div class="contact-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="contact-content">
                            <h4>Call Us:</h4>
                            <p><a href="tel:{{ $settings['phone'] ?? '' }}">{{ $settings['phone'] ?? '' }}</a></p>
                        </div>
                    </div>
                </div>

                {{-- Email --}}
                <div class="col-lg-3 col-md-6 col-sm-12">
                    <div class="contact-box">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="contact-content">
                            <h4>Email:</h4>
                            <p><a href="mailto:{{ $settings['email'] ?? '' }}">{{ $settings['email'] ?? '' }}</a></p>
                        </div>
                    </div>
                </div>

                {{-- Address --}}
                <div class="col-lg-3 col-md-6 col-sm-12">
                    <div class="contact-box">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="contact-content">
                            <h4>Address:</h4>
                            <p>{{ $settings['address'] ?? '' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Social Media --}}
                <div class="col-lg-3 col-md-6 col-sm-12 col-12">
                    <div class="contac-box">
                        <div class="contact-content">
                            <h4>connect with us</h4>
                        </div>
                        <div class="social-media-link">
                            <ul>
                                @if(!empty($settings['facebook']))
                                    <li><a href="{{ $settings['facebook'] }}" class="social-media-link-item" target="_blank"><i class="fab fa-facebook-f"></i></a></li>
                                @endif
                                @if(!empty($settings['twitter']))
                                    <li><a href="{{ $settings['twitter'] }}" class="social-media-link-item" target="_blank"><img src="{{ asset('assets/images/Contact/twitter.png') }}" alt="" class="img-fluid"></a></li>
                                @endif
                                @if(!empty($settings['linkedin']))
                                    <li><a href="{{ $settings['linkedin'] }}" class="social-media-link-item" target="_blank"><i class="fab fa-linkedin-in"></i></a></li>
                                @endif
                                @if(!empty($settings['instagram']))
                                    <li><a href="{{ $settings['instagram'] }}" class="social-media-link-item" target="_blank"><i class="fab fa-instagram"></i></a></li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- Enquiry Form --}}
    <section class="enquiry-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-10 col-md-12 mx-auto">
                    <div class="enquiry-form-wrapper">

                        <div class="enquiry-header text-center m-b-40">
                            <h2>Get Expert Assistance Today</h2>
                            <p>We'll contact you within 24 hours.</p>
                            <span class="enquiry-line"></span>
                        </div>

                        <form class="enquiry-form">
                            <div class="row">

                                {{-- Full Name --}}
                                <div class="col-md-6 mb-4">
                                    <div class="enquiry-form-group">
                                        <label>Full Name *</label>
                                        <div class="input-with-icon">
                                            <i class="far fa-user"></i>
                                            <input type="text" placeholder="Enter your full name" required>
                                        </div>
                                    </div>
                                </div>

                                {{-- Email Address --}}
                                <div class="col-md-6 mb-4">
                                    <div class="enquiry-form-group">
                                        <label>Email Address *</label>
                                        <div class="input-with-icon">
                                            <i class="far fa-envelope"></i>
                                            <input type="email" placeholder="Enter your email" required>
                                        </div>
                                    </div>
                                </div>

                                {{-- Nationality --}}
                                <div class="col-md-6 mb-4">
                                    <div class="enquiry-form-group">
                                        <label>Nationality *</label>
                                        <div class="input-with-icon">
                                            <i class="fas fa-globe"></i>
                                            <input type="text" placeholder="e.g. UAE, India" required>
                                        </div>
                                    </div>
                                </div>

                                {{-- Location --}}
                                <div class="col-md-6 mb-4">
                                    <div class="enquiry-form-group">
                                        <label>Location *</label>
                                        <div class="input-with-icon">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <input type="text" placeholder="City or Country" required>
                                        </div>
                                    </div>
                                </div>

                                {{-- WhatsApp Number --}}
                                <div class="col-md-6 mb-4">
                                    <div class="enquiry-form-group">
                                        <label>WhatsApp Number *</label>
                                        <div class="input-with-icon">
                                            <i class="fab fa-whatsapp"></i>
                                            <input type="tel" placeholder="+971 50 XXXXXXX" required>
                                        </div>
                                    </div>
                                </div>

                                {{-- Contact Number --}}
                                <div class="col-md-6 mb-4">
                                    <div class="enquiry-form-group">
                                        <label>Contact Number</label>
                                        <div class="input-with-icon">
                                            <i class="fas fa-phone-plus"></i>
                                            <input type="tel" placeholder="+971 50 XXXXXXX">
                                        </div>
                                    </div>
                                </div>

                                {{-- Service Category --}}
                                <div class="col-md-6 mb-4">
                                    <div class="enquiry-form-group">
                                        <label>Service Category *</label>
                                        <div class="input-with-icon">
                                            <i class="fas fa-briefcase"></i>
                                            <select required>
                                                <option value="" disabled selected>Select a Service</option>
                                                <option value="Visa">Visa Services</option>
                                                <option value="Finance">Finance Services</option>
                                                <option value="Residency">Residency</option>
                                                <option value="Company Setup">Company Setup</option>
                                                <option value="Others">Others</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                {{-- Preferred Language --}}
                                <div class="col-md-6 mb-4">
                                    <div class="enquiry-form-group">
                                        <label>Preferred Language</label>
                                        <div class="input-with-icon">
                                            <i class="fas fa-language"></i>
                                            <select>
                                                <option value="English" selected>English</option>
                                                <option value="Arabic">Arabic</option>
                                                <option value="Hindi">Hindi</option>
                                                <option value="Urdu">Urdu</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                {{-- Specific Requirements --}}
                                <div class="col-md-12 mb-4">
                                    <div class="enquiry-form-group">
                                        <label>Specific Requirements</label>
                                        <div class="input-with-icon align-items-start">
                                            <i class="fas fa-comment-dots mt-3"></i>
                                            <textarea rows="4" placeholder="How can we help you?"></textarea>
                                        </div>
                                    </div>
                                </div>

                                {{-- Submit Button and WhatsApp Link --}}
                                <div class="col-md-12 text-center mt-2">
                                    <button type="submit" class="btn-enquiry-submit">
                                        Submit Enquiry <i class="fas fa-paper-plane ms-2"></i>
                                    </button>
                                    <div class="mt-4 quick-contact">
                                        Or contact us instantly via <a href="https://wa.me/{{ preg_replace('/\D/', '', $settings['phone'] ?? '') }}" target="_blank" class="whatsapp-link"><i class="fab fa-whatsapp"></i> WhatsApp</a>
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
