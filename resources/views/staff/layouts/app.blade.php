<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>@yield('title', 'Dashboard') | GMS Staff</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="#">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        :root {
            --sidebar-bg: #1a3a5c;
            --sidebar-width: 250px;
            --header-height: 70px;
            --primary: #2563eb;
        }
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; background: #f5f6f8; }
        .vertical-menu {
            width: var(--sidebar-width); position: fixed; top: 0; left: 0; bottom: 0;
            background: var(--sidebar-bg); z-index: 1001; overflow-y: auto;
        }
        .navbar-brand-box {
            padding: 0 1.5rem; height: var(--header-height); display: flex;
            align-items: center; background: rgba(255,255,255,0.05);
        }
        .navbar-brand-box .logo-text { color: #fff; font-size: 1.15rem; font-weight: 700; }
        .navbar-brand-box .logo-text span { color: #60a5fa; }
        .navbar-brand-box .role-tag {
            color: #93c5fd; font-size: 11px; text-transform: uppercase;
            letter-spacing: 1px; display: block; margin-top: 2px;
        }
        #sidebar-menu { padding: 10px 0 30px; }
        #sidebar-menu .menu-title {
            padding: 12px 20px; letter-spacing: .05em; font-size: 11px;
            text-transform: uppercase; color: #7c9ab8; font-weight: 600;
        }
        #sidebar-menu ul { list-style: none; padding: 0; margin: 0; }
        #sidebar-menu ul li a {
            display: flex; align-items: center; padding: 10px 20px;
            color: #a6c0d8; font-size: 14px; text-decoration: none; transition: all 0.2s;
        }
        #sidebar-menu ul li a i { min-width: 2rem; font-size: 1.1rem; }
        #sidebar-menu ul li a:hover, #sidebar-menu ul li a.active { color: #fff; background: rgba(255,255,255,0.07); }
        #sidebar-menu ul li a.active { border-left: 3px solid #60a5fa; }
        .page-topbar {
            position: fixed; top: 0; left: var(--sidebar-width); right: 0;
            height: var(--header-height); background: #fff;
            box-shadow: 0 0.75rem 1.5rem rgba(18,38,63,.03); z-index: 1000;
            display: flex; align-items: center; justify-content: space-between; padding: 0 1.5rem;
        }
        .page-topbar .topbar-title { font-size: 1rem; font-weight: 600; color: #495057; }
        .main-content { margin-left: var(--sidebar-width); min-height: 100vh; }
        .page-content { padding: calc(var(--header-height) + 24px) 24px 60px; }
        .card { border: none; box-shadow: 0 0.75rem 1.5rem rgba(18,38,63,.03); border-radius: .5rem; margin-bottom: 1.5rem; }
        .card-title { font-size: 15px; margin-bottom: 0; font-weight: 600; }
        .badge-soft-success { background: rgba(52,195,143,.18); color: #34c38f; }
        .badge-soft-danger  { background: rgba(244,106,106,.18); color: #f46a6a; }
        .badge-soft-warning { background: rgba(241,180,76,.18); color: #f1b44c; }
        .badge-soft-primary { background: rgba(37,99,235,.18); color: #2563eb; }
        .badge-soft-info    { background: rgba(80,165,241,.18); color: #50a5f1; }
        .badge-soft-secondary { background: rgba(108,117,125,.18); color: #6c757d; }
        .badge-soft-teal    { background: rgba(20,184,166,.18); color: #0d9488; }
        .footer {
            position: fixed; bottom: 0; left: var(--sidebar-width); right: 0;
            padding: 12px 24px; background: #fff; border-top: 1px solid #eff2f7;
            font-size: 13px; color: #74788d;
        }
        .table thead th { background: #f8f9fa; border-bottom: 2px solid #eff2f7; font-weight: 600; font-size: 13px; color: #495057; }
        .step-indicator { display: flex; justify-content: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 4px; }
        .step-indicator .step {
            display: flex; align-items: center; padding: 8px 14px; background: #f5f6f8;
            border-radius: 4px; font-size: 13px; color: #74788d; cursor: pointer; transition: all 0.2s;
        }
        .step-indicator .step.active { background: var(--primary); color: #fff; }
        .step-indicator .step.completed { background: #34c38f; color: #fff; }
        .step-indicator .step i { margin-right: 6px; }
        .tab-pane { display: none; }
        .tab-pane.active { display: block; }
        .service-row { background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 10px; }
        @media (max-width: 992px) {
            .vertical-menu { display: none; }
            .main-content { margin-left: 0; }
            .page-topbar { left: 0; }
            .footer { left: 0; }
        }
    </style>
    @yield('styles')
    <!-- Alpine.js (required for softphone widget) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body>
    <div class="vertical-menu">
        <div class="navbar-brand-box">
            <div>
                <span class="logo-text">Get<span>My</span>Serve</span>
                <span class="role-tag">Staff Portal</span>
            </div>
        </div>
        @include('staff.layouts.sidebar')
    </div>

    <div class="page-topbar">
        <div class="topbar-title">@yield('page-title', 'Dashboard')</div>
        <div class="d-flex align-items-center">
            <div class="dropdown">
                <button class="btn btn-light btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="mdi mdi-account-circle me-1"></i> {{ Auth::user()->name }}
                    <span class="badge badge-soft-primary ms-1" style="font-size:10px;">Staff</span>
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="dropdown-item text-danger" type="submit">
                            <i class="mdi mdi-logout me-2"></i>Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="main-content">
        <div class="page-content">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @yield('content')
        </div>
    </div>

    <footer class="footer">
        <div class="row">
            <div class="col-sm-6">&copy; {{ date('Y') }} GetMyServe</div>
            <div class="col-sm-6 text-end">GMS Staff Portal</div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    {{-- ZIWO Softphone (only for staff with a linked call agent) --}}
    @include('admin.partials.softphone')

    @yield('scripts')
</body>
</html>
