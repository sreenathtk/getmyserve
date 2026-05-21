<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Get My Serv | Dubai\'s Trusted Service Marketplace')</title>
    @yield('meta')
    @include('layouts.links')
</head>

<body>

    @include('layouts.header')

    @if(session('enquiry_success'))
        <div class="container mt-3">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i>{{ session('enquiry_success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    @yield('content')

    @include('layouts.footer')

    @include('layouts.script')

</body>

</html>
