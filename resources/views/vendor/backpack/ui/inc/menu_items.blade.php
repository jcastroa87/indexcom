{{-- This file is used to store sidebar items, inside the Backpack admin panel --}}
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>

<!-- Exchange Indices Management Section -->
<x-backpack::menu-dropdown title="Index" icon="la la-key">
    <x-backpack::menu-dropdown-header title="Exchange Indices" />
    <x-backpack::menu-dropdown-item title="Indices" icon="la la-chart-bar" :link="backpack_url('index')" />
    <x-backpack::menu-dropdown-item title="Rates" icon="la la-exchange-alt" :link="backpack_url('rate')" />
</x-backpack::menu-dropdown>

<!-- Users Management Section -->
<x-backpack::menu-dropdown title="Access" icon="la la-key">
    <x-backpack::menu-dropdown-header title="Users" />
    <x-backpack::menu-dropdown-item title="Users" icon="la la-user" :link="backpack_url('user')" />
    <x-backpack::menu-dropdown-item title="Roles" icon="la la-id-badge" :link="backpack_url('role')" />
    <x-backpack::menu-dropdown-item title="Permissions" icon="la la-key" :link="backpack_url('permission')" />
</x-backpack::menu-dropdown>

<!-- API Management Section -->
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('api-key') }}"><i class="nav-icon la la-key"></i> API Keys</a></li>

<!-- Subscription Management Section -->
<x-backpack::menu-dropdown title="Subscriptions" icon="la la-credit-card">
    <x-backpack::menu-dropdown-header title="Plan Management" />
    <x-backpack::menu-dropdown-item title="Plans" icon="la la-tags" :link="backpack_url('plan')" />
    <x-backpack::menu-dropdown-item title="Subscriptions" icon="la la-calendar-check" :link="backpack_url('subscription')" />

    <x-backpack::menu-dropdown-header title="Analytics" />
    <x-backpack::menu-dropdown-item title="API Usage" icon="la la-chart-line" :link="backpack_url('analytics')" />
    <x-backpack::menu-dropdown-item title="Subscription Metrics" icon="la la-chart-pie" :link="backpack_url('analytics/subscriptions')" />
    <x-backpack::menu-dropdown-item title="Plan Optimization" icon="la la-lightbulb" :link="backpack_url('analytics/plan-optimization')" />
</x-backpack::menu-dropdown>
