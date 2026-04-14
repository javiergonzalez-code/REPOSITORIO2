@props(['user' => null])

@php
    // Extraemos las propiedades de forma segura. Si no hay usuario, ponemos valores por defecto.
    $name = $user ? ($user->name ?? 'Usuario') : 'Desconocido';
    $subtitle = $user ? ($user->role ?? 'N/A') : 'N/A';
    $userId = $user ? ($user->id ?? 0) : 0;
    
    // Obtenemos la primera letra para el círculo del avatar
    $initial = strtoupper(substr($name, 0, 1));
@endphp

<div class="d-flex align-items-center">
    <div class="avatar-pill bg-blue-ragon-gradient shadow-sm" style="width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
        {{ $initial }}
    </div>
    <div class="ms-3">
        <div class="fw-bold text-main mb-0" style="font-size: 0.9rem;">
            {{ $name }}
        </div>
        <div class="x-small text-muted" style="font-size: 0.75rem;">
            {{ strtoupper($subtitle) }}
        </div>
    </div>
</div>