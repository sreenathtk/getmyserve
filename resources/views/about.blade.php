@extends('layouts.app')

@section('title', 'Get My Serv | Connecting You to Trusted Service Providers in UAE')

@section('meta')
    <meta name="description"
        content="Learn how Get My Serv simplifies access to verified service providers across Dubai, helping individuals and businesses find reliable solutions quickly.">
    <meta name="keywords" content="UAE service, Dubai service company, business support Dubai">
    <link rel="canonical" href="#">
    <meta property="og:locale" content="en_IN" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="Service Connections | Get My Serv" />
    <meta property="og:url" content="PAGE_URL" />
    <meta property="og:site_name" content="Drunken Duck" />
    <meta property="og:image" content="#" />
    <meta property="og:image:width" content="1200" />
    <meta property="og:image:height" content="550" />
    <meta property="og:description"
        content="We bridge the gap between customers and trusted service providers across the UAE." />
    <meta name="robots" content="index, follow" />
@endsection

@section('content')

    {{-- About Banner --}}
    <section class="about-banner ban-about">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="about-banner-content">
                        <h2>About Us</h2>
                        <h4><a href="{{ url('/') }}">home</a> <span>// About</span></h4>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- About Stats --}}
    <section class="about-stats-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">

                    <div class="visa-header">
                        <div class="visa-header-left">
                            <h2>About GetMyServ</h2>
                        </div>
                    </div>

                    <div class="visa-header-text">
                        <p class="v-h-t">Get My Serv (GMS) is a gateway designed to
                            simplify access to professional services. More than just
                            a directory, GMS is a unified ecosystem that brings a
                            wide range of professional service providers under one
                            roof. By leveraging technology, GMS bridges the gap between service providers and clients.
                        </p>
                        <p class="v-h-tb">Customers can easily choose from verified service providers, businesses can efficiently
                            outsource required services, and professionals can register and offer their expertise through this marketplace.
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </section>

    {{-- Mission & Vision --}}
    <section class="mission-vision">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                    <div class="mission-card">
                        <div class="mission-card-icon"><i class="fas fa-rocket"></i></div>
                        <div class="mission-card-header">
                            <h3>mission</h3>
                        </div>
                        <div class="mission-card-body">
                            <p>One Platform for All Professional Services.
                                We've consolidated the entire service industry into
                                a smart, SaaS-enabled gateway. We don't just connect you; we secure your journey.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                    <div class="mission-card">
                        <div class="mission-card-icon"><i class="fas fa-eye"></i></div>
                        <div class="mission-card-header">
                            <h3>vision</h3>
                        </div>
                        <div class="mission-card-body">
                            <p>Simplifying Life & Business.
                                To provide a unified, fast, and reliable ecosystem for every professional service</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
