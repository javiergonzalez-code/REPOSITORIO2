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

{{-- Sección de Administración de Usuarios, Seguridad y Auditoría --}}
{{-- SOLO visible para admin@ragon.com --}}
@if(backpack_user()->email === 'admin@ragon.com' || backpack_user()->role === 'superadmin')
    
    {{-- Usuarios --}}
    <x-backpack::menu-item title="Usuarios" icon="la la-user" :link="backpack_url('user')" />

    {{-- Seguridad (Roles y Permisos) --}}
    <x-backpack::menu-dropdown title="Seguridad" icon="la la-shield">
        <x-backpack::menu-dropdown-item title="Roles" icon="la la-id-badge" :link="backpack_url('role')" />
        <x-backpack::menu-dropdown-item title="Permisos" icon="la la-key" :link="backpack_url('permission')" />
    </x-backpack::menu-dropdown>

    {{-- Auditoría (Logs) --}}
    <x-backpack::menu-item title="Auditoría" icon="la la-history" :link="backpack_url('log')" />

@endif

{{-- Sección de Archivos: Visible para administradores normales según sus permisos --}}
@can('list archivos')
    <x-backpack::menu-item title="Archivos" icon="la la-file-archive" :link="backpack_url('archivo')" />
@endcan