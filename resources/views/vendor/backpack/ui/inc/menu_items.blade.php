{{-- Inicio / Dashboard --}}
<x-backpack::menu-item 
    title="{{ trans('backpack::base.dashboard') }}" 
    icon="la la-home" 
    :link="backpack_url('dashboard')" />

{{-- Sección de Operaciones --}}
<div class="nav-heading small text-muted uppercase px-3 pt-3 pb-1">Gestión de Datos</div>

<x-backpack::menu-item 
    title="Archivos" 
    icon="la la-file-archive" 
    :link="backpack_url('archivo')" />

<x-backpack::menu-item 
    title="Logs del Sistema" 
    icon="la la-history" 
    :link="backpack_url('log')" />

{{-- Sección de Configuración --}}
<div class="nav-heading small text-muted uppercase px-3 pt-3 pb-1">Administración</div>

<x-backpack::menu-dropdown title="Seguridad" icon="la la-shield-alt">
    <x-backpack::menu-item title="Usuarios" icon="la la-user" :link="backpack_url('user')" />
    <x-backpack::menu-item title="Roles" icon="la la-id-badge" :link="backpack_url('role')" />
    <x-backpack::menu-item title="Permisos" icon="la la-key" :link="backpack_url('permission')" />
</x-backpack::menu-dropdown>