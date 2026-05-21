<div id="sidebar-menu">
    <ul>
        <li class="menu-title">Main</li>
        <li>
            <a href="{{ route('provider.dashboard') }}" class="{{ request()->routeIs('provider.dashboard') ? 'active' : '' }}">
                <i class="ri-dashboard-line"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <li class="menu-title">Work</li>
        <li>
            <a href="{{ route('provider.enquiries.index') }}" class="{{ request()->routeIs('provider.enquiries.*') ? 'active' : '' }}">
                <i class="ri-mail-send-line"></i>
                <span>Service Requests</span>
                @php
                    $spPendingCount = \App\Models\Enquiry::where('assigned_sp_id', auth()->id())
                        ->whereIn('status', ['pending', 'in_progress'])
                        ->count();
                @endphp
                @if($spPendingCount > 0)
                    <span class="badge badge-soft-danger ms-auto">{{ $spPendingCount }}</span>
                @endif
            </a>
        </li>
        <li>
            <a href="{{ route('provider.orders.index') }}" class="{{ request()->routeIs('provider.orders.*') ? 'active' : '' }}">
                <i class="ri-shopping-bag-line"></i>
                <span>Orders</span>
                @php
                    $spActiveOrderCount = \App\Models\Order::where('assigned_sp_id', auth()->id())
                        ->whereIn('status', ['paid', 'processing'])
                        ->count();
                @endphp
                @if($spActiveOrderCount > 0)
                    <span class="badge badge-soft-info ms-auto">{{ $spActiveOrderCount }}</span>
                @endif
            </a>
        </li>

        <li class="menu-title">Profile</li>
        <li>
            <a href="{{ route('provider.change-requests.create') }}" class="{{ request()->routeIs('provider.change-requests.*') ? 'active' : '' }}">
                <i class="ri-file-edit-line"></i>
                <span>Profile Update Request</span>
            </a>
        </li>
        <li>
            <a href="{{ route('provider.change-password') }}" class="{{ request()->routeIs('provider.change-password') ? 'active' : '' }}">
                <i class="ri-lock-password-line"></i>
                <span>Change Password</span>
            </a>
        </li>
    </ul>
</div>
