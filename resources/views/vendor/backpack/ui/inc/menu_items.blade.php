{{-- Botón para regresar al Panel Principal del sistema --}}
<li class="nav-item">
    <a class="nav-link text-primary fw-bold" href="{{ url('/home') }}">
        <i class="la la-arrow-left nav-icon"></i> Regresar al Panel de Control
    </a>
</li>
{{-- Dashboard --}}
<li class="nav-item">
    <a class="nav-link" href="{{ backpack_url('dashboard') }}">
        <i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}
    </a>
</li>

{{-- Sección de Administración de Usuarios --}}
@can('list users')
    <x-backpack::menu-item title="Usuarios" icon="la la-user" :link="backpack_url('user')" />
@endcan

{{-- Sección de Seguridad (Roles y Permisos) --}}
@if (backpack_user()->hasRole('admin') || backpack_user()->can('manage roles'))
    <x-backpack::menu-dropdown title="Seguridad" icon="la la-shield">
        <x-backpack::menu-dropdown-item title="Roles" icon="la la-id-badge" :link="backpack_url('role')" />

        <x-backpack::menu-dropdown-item title="Permisos" icon="la la-key" :link="backpack_url('permission')" />
    </x-backpack::menu-dropdown>
@endif

{{-- Sección de Auditoría (Logs) --}}
@can('list logs')
    <x-backpack::menu-item title="Auditoría" icon="la la-history" :link="backpack_url('log')" />
@endcan

{{-- Sección de Archivos --}}
@can('list archivos')
    {{-- Asumiendo que existe este permiso --}}
    <x-backpack::menu-item title="Archivos" icon="la la-file-archive" :link="backpack_url('archivo')" />
@endcan
