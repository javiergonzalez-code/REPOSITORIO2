{{-- Dashboard --}}
<li class="nav-item">
    <a class="nav-link" href="{{ backpack_url('dashboard') }}">
        <i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}
    </a>
</li>

{{-- Sección de Administración de Accesos (Spatie + Backpack) --}}
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" role="button" aria-expanded="true">
        <i class="la la-users nav-icon"></i> Autenticación
    </a>
    <div class="dropdown-menu" data-bs-popper="static">
        <a class="dropdown-item" href="{{ backpack_url('user') }}">
            <i class="la la-user nav-icon"></i> Usuarios
        </a>
        <a class="dropdown-item" href="{{ backpack_url('role') }}">
            <i class="la la-id-badge nav-icon"></i> Roles
        </a>
        <a class="dropdown-item" href="{{ backpack_url('permission') }}">
            <i class="la la-key nav-icon"></i> Permisos
        </a>
    </div>
</li>

{{-- Otros Módulos --}}
<li class="nav-item">
    <a class="nav-link" href="{{ backpack_url('archivo') }}">
        <i class="la la-file nav-icon"></i> Archivos
    </a>
</li>

<li class="nav-item">
    <a class="nav-link" href="{{ backpack_url('log') }}">
        <i class="la la-history nav-icon"></i> Logs
    </a>
</li>