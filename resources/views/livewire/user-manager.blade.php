<?php
use function Livewire\Volt\{state, computed, usesPagination};
use App\Models\User;

usesPagination(theme: 'bootstrap');

// Restauramos el userFilter para el nombre y dejamos search para el correo
state(['search' => '', 'userFilter' => '', 'roleFilter' => '']);

// Lógica inteligente para la lista desplegable de usuarios
$sugerencias_usuarios = computed(function () {
    if (strlen($this->userFilter) < 1) {
        return collect();
    }
    $sugerencias = User::select('name')
        ->where('name', 'like', "%{$this->userFilter}%")
        ->orderBy('name', 'asc')
        ->take(6)
        ->get();
    if ($sugerencias->count() === 1 && strtolower($sugerencias->first()->name) === strtolower($this->userFilter)) {
        return collect();
    }
    return $sugerencias;
});

// Computar la consulta principal reactiva
$usuarios = computed(function () {
    $query = User::query();

    if ($this->search) {
        $query->where('email', 'like', "%{$this->search}%");
    }
    if ($this->userFilter) {
        $query->where('name', 'like', "%{$this->userFilter}%");
    }
    if ($this->roleFilter) {
        $query->where('role', $this->roleFilter);
    }

    return $query->latest()->paginate(10);
});
?>

<div>
    <div class="mb-3 text-end" style="font-size: 0.8rem; color: #64748b; font-weight: 700;">
        {{ $this->usuarios->total() }} USUARIOS ENCONTRADOS
    </div>

    {{-- RECUADRO DE FILTROS --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white custom-card">
        <div
            class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
            <h6 class="text-uppercase fw-black mb-0 text-muted" style="font-size: 1rem; letter-spacing: 1px;">
                <i class="fas fa-filter me-2"></i> Gestión y Filtros
            </h6>
            <a href="{{ route('users.create') }}" class="btn btn-primary rounded-pill shadow-sm fw-bold px-4">
                <i class="fas fa-plus-circle me-2"></i> NUEVO USUARIO
            </a>
        </div>
        <div class="card-body p-4">
            <div class="row g-3 align-items-end">

                {{-- 1. Filtro Nombre (Con autocompletado inteligente) --}}
                <div class="col-lg-3 col-md-6">
                    <label class="form-label-custom text-uppercase x-small fw-bold">Nombre de Usuario</label>
                    <div class="position-relative">
                        <i class="fas fa-user text-muted position-absolute top-50 start-0 translate-middle-y ms-3"></i>
                        <input type="text" wire:model.live.debounce.300ms="userFilter" class="form-control ps-5"
                            placeholder="Escribir nombre..." autocomplete="off">

                        {{-- La lista desplegable que arreglamos en el paso 2 --}}
                        @if (count($this->sugerencias_usuarios) > 0)
                            <div class="position-absolute w-100 border rounded-3 shadow-lg mt-1 overflow-hidden custom-dropdown-box"
                                style="z-index: 1050;">
                                <ul class="list-unstyled mb-0">
                                    @foreach ($this->sugerencias_usuarios as $sugerencia)
                                        <li>
                                            <button type="button"
                                                class="dropdown-item py-2 px-3 text-start w-100 border-0 custom-dropdown-btn"
                                                wire:click="$set('userFilter', '{{ $sugerencia->name }}')">
                                                <i class="fas fa-user-circle text-primary me-2"></i>
                                                {{ $sugerencia->name }}
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- 2. Búsqueda por Email --}}
                <div class="col-lg-3 col-md-6">
                    <label class="form-label-custom text-uppercase x-small fw-bold">Correo Electrónico</label>
                    <div class="position-relative">
                        <i class="fas fa-at text-muted position-absolute top-50 start-0 translate-middle-y ms-3"></i>
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control ps-5"
                            placeholder="Ej: admin@ragon.com...">
                    </div>
                </div>

                {{-- 3. Filtro de Rol --}}
                <div class="col-lg-4 col-md-6">
                    <label class="form-label-custom text-uppercase x-small fw-bold">Nivel de Acceso</label>
                    <select wire:model.live="roleFilter" class="form-control">
                        <option value="">Todos los roles</option>
                        <option value="superadmin">Super Administrador</option>
                        <option value="admin">Administrador</option>
                        <option value="proveedor">Proveedor</option>
                    </select>
                </div>

                {{-- 4. Botón Limpiar --}}
                <div class="col-lg-2 col-md-12">
                    <button wire:click="$set('search', ''); $set('userFilter', ''); $set('roleFilter', '')"
                        class="btn btn-outline-secondary rounded-pill w-100 fw-bold">
                        <i class="fas fa-eraser me-1"></i> Limpiar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- RECUADRO DE TABLA DE USUARIOS --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white custom-card"
        style="overflow: visible; position: relative; z-index: 1050;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 border-top-0">
                <thead style="background: #f8fafc;">
                    <tr class="text-uppercase"
                        style="font-size: 0.7rem; font-weight: 700; color: #64748b; letter-spacing: 0.5px;">
                        <th class="ps-4 py-3 border-0">Usuario</th>
                        <th class="py-3 border-0">Nivel de Acceso (Rol)</th>
                        <th class="text-center py-3 border-0">Fecha de Registro</th>
                        <th class="text-end pe-4 py-3 border-0">Acciones</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($this->usuarios as $user)
                        <tr style="transition: all 0.2s ease;">

                            <td class="ps-4 py-3">
                                <x-user-avatar :name="$user->name" :userId="$user->id" :subtitle="$user->email" />
                            </td>

                            {{-- AQUÍ ESTÁ LA SOLUCIÓN DEL MODO OSCURO (USANDO NUESTRAS CLASES DE CSS) --}}
                            <td class="py-3">
                                @php
                                    $roleClass = match (strtolower($user->role)) {
                                        'superadmin' => 'role-superadmin',
                                        'admin' => 'role-admin',
                                        'proveedor' => 'role-proveedor',
                                        default => 'role-default',
                                    };
                                    $roleIcon = match (strtolower($user->role)) {
                                        'superadmin' => 'fa-crown',
                                        'admin' => 'fa-user-shield',
                                        'proveedor' => 'fa-truck',
                                        default => 'fa-user',
                                    };
                                @endphp
                                <span
                                    class="d-inline-flex align-items-center rounded-pill fw-bold shadow-sm px-3 py-1 {{ $roleClass }}"
                                    style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                    <i class="fas {{ $roleIcon }} me-2"></i> {{ strtoupper($user->role ?? 'N/A') }}
                                </span>
                            </td>

                            <td class="text-center py-3">
                                <span class="fw-bold d-block text-main mb-0"
                                    style="font-size: 0.85rem;">{{ $user->created_at->format('d/m/Y') }}</span>
                                <span class="x-small text-muted font-monospace">Hace
                                    {{ $user->created_at->diffForHumans(null, true) }}</span>
                            </td>

                            <td class="text-end pe-4 py-3">
                                <x-table-actions editRoute="{{ route('users.edit', $user->id) }}"
                                    deleteRoute="{{ route('users.destroy', $user->id) }}" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="py-4">
                                    <i class="fas fa-users-slash fa-3x text-muted mb-3 opacity-25"></i>
                                    <h5 class="text-muted fw-bold">No se encontraron usuarios</h5>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4 d-flex justify-content-center">
        {{ $this->usuarios->links('pagination::bootstrap-5') }}
    </div>

    <x-delete-confirm-script />
</div>
