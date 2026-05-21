<div id="sidebar-menu">
    <ul>
        <li class="menu-title">Main</li>
        <li>
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="ri-dashboard-line"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <li class="menu-title">Management</li>
        <li>
            <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                <i class="ri-folder-line"></i>
                <span>Categories</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.services.index') }}" class="{{ request()->routeIs('admin.services.*') ? 'active' : '' }}">
                <i class="ri-stack-line"></i>
                <span>Services</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.service-providers.index') }}" class="{{ request()->routeIs('admin.service-providers.*') ? 'active' : '' }}">
                <i class="ri-building-2-line"></i>
                <span>Service Providers</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.customers.index') }}" class="{{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
                <i class="ri-user-3-line"></i>
                <span>Customers</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.staff.index') }}" class="{{ request()->routeIs('admin.staff.*') ? 'active' : '' }}">
                <i class="ri-team-line"></i>
                <span>Staff</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.offers.index') }}" class="{{ request()->routeIs('admin.offers.*') ? 'active' : '' }}">
                <i class="ri-price-tag-3-line"></i>
                <span>Offers</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.orders.index') }}" class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                <i class="ri-shopping-bag-line"></i>
                <span>Orders</span>
                @php $pendingRefunds = \App\Models\Order::where('refund_status','requested')->count(); @endphp
                @if($pendingRefunds > 0)
                    <span class="badge badge-soft-warning ms-auto">{{ $pendingRefunds }}</span>
                @endif
            </a>
        </li>
        <li>
            <a href="{{ route('admin.payments.index') }}" class="{{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                <i class="ri-bank-card-line"></i>
                <span>Payments</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.enquiries.index') }}" class="{{ request()->routeIs('admin.enquiries.*') ? 'active' : '' }}">
                <i class="ri-mail-send-line"></i>
                <span>Service Requests</span>
                @php $pendingEnqCount = \App\Models\Enquiry::where('status','pending')->count(); @endphp
                @if($pendingEnqCount > 0)
                    <span class="badge badge-soft-danger ms-auto">{{ $pendingEnqCount }}</span>
                @endif
            </a>
        </li>
        <li>
            <a href="{{ route('admin.assistance-requests.index') }}" class="{{ request()->routeIs('admin.assistance-requests.*') ? 'active' : '' }}">
                <i class="ri-customer-service-2-line"></i>
                <span>Assistance Requests</span>
                @php $pendingARCount = \App\Models\AssistanceRequest::where('status','pending')->count(); @endphp
                @if($pendingARCount > 0)
                    <span class="badge badge-soft-danger ms-auto">{{ $pendingARCount }}</span>
                @endif
            </a>
        </li>
        <li>
            <a href="{{ route('admin.change-requests.index') }}" class="{{ request()->routeIs('admin.change-requests.*') ? 'active' : '' }}">
                <i class="ri-file-list-3-line"></i>
                <span>Change Requests</span>
                @php $pendingCRCount = \App\Models\ServiceProviderChangeRequest::where('status','pending')->count(); @endphp
                @if($pendingCRCount > 0)
                    <span class="badge badge-soft-warning ms-auto">{{ $pendingCRCount }}</span>
                @endif
            </a>
        </li>

        <li class="menu-title">Configuration</li>
        <li>
            <a href="{{ route('admin.settings.edit') }}" class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                <i class="ri-settings-3-line"></i>
                <span>General Settings</span>
            </a>
        </li>

        <li class="menu-title">WhatsApp</li>
        <li>
            <a href="{{ route('admin.whatsapp.templates.index') }}" class="{{ request()->routeIs('admin.whatsapp.*') ? 'active' : '' }}">
                <i class="ri-whatsapp-line"></i>
                <span>Templates</span>
                @php
                    $rejectedCount = \App\Models\WatiTemplate::where('status', 'REJECTED')->count();
                @endphp
                @if($rejectedCount > 0)
                    <span class="badge badge-soft-danger ms-auto">{{ $rejectedCount }}</span>
                @endif
            </a>
        </li>

        <li class="menu-title">Call Center</li>
        <li>
            <a href="{{ route('admin.call-logs.index') }}" class="{{ request()->routeIs('admin.call-logs.*') ? 'active' : '' }}">
                <i class="ri-phone-line"></i>
                <span>Call Logs</span>
                @php $missedCalls = \App\Models\CallLog::where('status','missed')->whereDate('started_at', today())->count(); @endphp
                @if($missedCalls > 0)
                    <span class="badge badge-soft-danger ms-auto">{{ $missedCalls }}</span>
                @endif
            </a>
        </li>
        <li>
            <a href="{{ route('admin.call-agents.index') }}" class="{{ request()->routeIs('admin.call-agents.*') ? 'active' : '' }}">
                <i class="ri-headphone-line"></i>
                <span>Call Agents</span>
            </a>
        </li>
    </ul>
</div>
