<div id="sidebar-menu">
    <ul>
        <li class="menu-title">Main</li>
        <li>
            <a href="{{ route('staff.dashboard') }}" class="{{ request()->routeIs('staff.dashboard') ? 'active' : '' }}">
                <i class="ri-dashboard-line"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <li class="menu-title">Work</li>
        <li>
            <a href="{{ route('staff.enquiries.index') }}" class="{{ request()->routeIs('staff.enquiries.*') ? 'active' : '' }}">
                <i class="ri-mail-send-line"></i>
                <span>Service Requests</span>
                @php
                    $staffPendingCount = 0;
                    if (auth()->check()) {
                        $assignments  = auth()->user()->staffAssignments;
                        $svcIds       = $assignments->whereNotNull('service_id')->pluck('service_id')->toArray();
                        $catIds       = $assignments->whereNull('service_id')->pluck('category_id')->toArray();
                        if (!empty($catIds)) {
                            $catSvcIds = \App\Models\Service::whereHas('subCategory', fn($q) => $q->whereIn('category_id', $catIds))->pluck('id')->toArray();
                            $svcIds    = array_unique(array_merge($svcIds, $catSvcIds));
                        }
                        $staffPendingCount = \App\Models\Enquiry::where('status', 'pending')->whereIn('service_id', $svcIds)->count();
                    }
                @endphp
                @if($staffPendingCount > 0)
                    <span class="badge badge-soft-danger ms-auto">{{ $staffPendingCount }}</span>
                @endif
            </a>
        </li>

        {{-- <li>
            <a href="{{ route('staff.orders.index') }}" class="{{ request()->routeIs('staff.orders.*') ? 'active' : '' }}">
                <i class="ri-shopping-bag-line"></i>
                <span>Orders</span>
                @php
                    $staffOrderCount = auth()->check()
                        ? \App\Models\Order::where('assigned_staff_id', auth()->id())
                            ->whereIn('status', ['paid','processing'])
                            ->count()
                        : 0;
                @endphp
                @if($staffOrderCount > 0)
                    <span class="badge badge-soft-info ms-auto">{{ $staffOrderCount }}</span>
                @endif
            </a>
        </li>

        <li>
            <a href="{{ route('staff.assistance-requests.index') }}" class="{{ request()->routeIs('staff.assistance-requests.*') ? 'active' : '' }}">
                <i class="ri-customer-service-2-line"></i>
                <span>Assistance Requests</span>
                @php
                    $arPendingCount = auth()->check()
                        ? \App\Models\AssistanceRequest::where('assigned_to', auth()->id())->where('status', 'pending')->count()
                        : 0;
                @endphp
                @if($arPendingCount > 0)
                    <span class="badge badge-soft-danger ms-auto">{{ $arPendingCount }}</span>
                @endif
            </a>
        </li> --}}

        <li class="menu-title">Customers</li>
        <li>
            <a href="{{ route('staff.customers.index') }}" class="{{ request()->routeIs('staff.customers.*') ? 'active' : '' }}">
                <i class="ri-group-line"></i>
                <span>Customers</span>
            </a>
        </li>

        {{-- <li class="menu-title">Service Providers</li>
        <li>
            <a href="{{ route('staff.change-requests.index') }}" class="{{ request()->routeIs('staff.change-requests.*') ? 'active' : '' }}">
                <i class="ri-file-list-3-line"></i>
                <span>My Requests</span>
            </a>
        </li>
        <li>
            <a href="{{ route('staff.change-requests.create', ['type' => 'create']) }}" class="">
                <i class="ri-add-circle-line"></i>
                <span>New Provider Request</span>
            </a>
        </li> --}}
    </ul>
</div>
