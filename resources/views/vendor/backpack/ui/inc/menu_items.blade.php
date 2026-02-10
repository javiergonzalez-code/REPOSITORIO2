{{-- This file is used for menu items by any Backpack v7 theme --}}
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>
<x-backpack::menu-dropdown title="Authentication" icon="la la-group">
    <x-backpack::menu-item title="Users" icon="la la-user" :link="backpack_url('user')" />
    <x-backpack::menu-item title="Roles" icon="la la-id-badge" :link="backpack_url('role')" />
    <x-backpack::menu-item title="Permissions" icon="la la-key" :link="backpack_url('permission')" />
</x-backpack::menu-dropdown>