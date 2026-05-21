@extends('layouts.app')

@section('title', 'Login | GetMyServ')

@section('content')

    {{-- Banner --}}
    <section class="about-banner ban-login">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="about-banner-content">
                        <h2>Login</h2>
                        <h4><a href="{{ url('/') }}">home</a> <span>// Login</span></h4>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Login Section --}}
    <section class="login-section">
        <div class="container">
            <div class="row align-items-center justify-content-center">
                <div class="col-lg-6 col-md-10 col-sm-12 col-12">
                    <div class="login-box">

                        <div class="login-header">
                            <h2>Sign In</h2>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger mb-3" style="font-size:14px;">
                                @foreach ($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif

                        @if (session('status'))
                            <div class="alert alert-success mb-3" style="font-size:14px;">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="email" name="email" id="email" placeholder="Email"
                                    class="{{ $errors->has('email') ? 'border-danger' : '' }}"
                                    value="{{ old('email') }}" required autofocus>
                            </div>

                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" name="password" id="password"
                                    placeholder="Password" required>
                            </div>

                            <button type="submit" class="login-btn">Sign In</button>
                        </form>

                        <div class="login-footer">
                            <p>Don't have an account? <a href="{{ route('register') }}">Register</a></p>
                            <p>By continuing you agree to our <a href="#">Terms &amp; Conditions</a> and <a href="#">Privacy Policy</a></p>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
