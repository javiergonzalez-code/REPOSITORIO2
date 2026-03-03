<?php
use function Livewire\Volt\{state, computed, usesPagination};
use App\Models\User;

usesPagination(theme: 'bootstrap');

// Variables reactivas de búsqueda
state(['search' => '', 'userFilter' => '', 'roleFilter' => '']);

// Función de seguridad importada de tu controlador
$rolesPermitidos = computed(function () {
    $user = auth()->user();
    if ($user->role === 'superadmin' || $user->email === 'admin@ragon.com') {
        return ['superadmin', 'admin', 'proveedor'];
    }
    return ['admin', 'proveedor'];
});

$usuarios_filtro = computed(fn() => User::select('name')->orderBy('name', 'asc')->get());

// Consulta dinámica
$users = computed(function () {
    $query = User::query();
    $permitidos = $this->rolesPermitidos;

    if (!in_array('superadmin', $permitidos)) {
        $query->where('role', '!=', 'superadmin');
    }

    if ($this->search) {
        $query->where(function ($q) {
            $q->where('name', 'like', "%{$this->search}%")
              ->orWhere('email', 'like', "%{$this->search}%")
              ->orWhere('id', 'like', "%{$this->search}%")
              ->orWhere('rfc', 'like', "%{$this->search}%");
        });
    }

    if ($this->userFilter) {
        $query->where('name', 'like', "%{$this->userFilter}%");
    }

    if ($this->roleFilter && in_array($this->roleFilter, $permitidos)) {
        $query->where('role', $this->roleFilter);
    }

    return $query->orderBy('name', 'asc')->paginate(10);
});
?>

<div>
    {{-- Contador reactivo --}}
    <div class="mb-3 text-end" style="font-size: 0.8rem; color: #64748b; font-weight: 700;">
        {{ $this->users->total() }} USUARIOS ENCONTRADOS
    </div>

    {{-- RECUADRO DE FILTROS (Sin form, usando wire:model.live) --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-12 col-md-6 col-lg-4">
                    <label class="form-label-custom text-uppercase x-small fw-bold">Búsqueda General</label>
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Nombre, email, RFC...">
                </div>
                
                <div class="col-12 col-md-6 col-lg-4">
                    <label class="form-label-custom text-uppercase x-small fw-bold">Filtrar por Nombre</label>
                    <input class="form-control" list="usuariosList" wire:model.live="userFilter" placeholder="Seleccionar usuario...">
                    <datalist id="usuariosList">
                        @foreach ($this->usuarios_filtro as $u)
                            <option value="{{ $u->name }}">
                        @endforeach
                    </datalist>
                </div>

                <div class="col-12 col-md-6 col-lg-4">
                    <label class="form-label-custom text-uppercase x-small fw-bold">Rol</label>
                    <select wire:model.live="roleFilter" class="form-control">
                        <option value="">Todos los roles</option>
                        @if(in_array('admin', $this->rolesPermitidos))
                            <option value="admin">Administrador</option>
                        @endif
                        @if(in_array('proveedor', $this->rolesPermitidos))
                            <option value="proveedor">Proveedor</option>
                        @endif
                    </select>
                </div>
            </div>
            
            {{-- Botón limpiar filtros --}}
            <div class="mt-3 d-flex justify-content-end">
                <button wire:click="$set('search', ''); $set('userFilter', ''); $set('roleFilter', '')" class="btn btn-outline-secondary rounded-pill">
                    <i class="fas fa-eraser me-2"></i> Limpiar Filtros
                </button>
            </div>
        </div>
    </div>

    {{-- RECUADRO DE LA TABLA (AQUÍ ESTÁ LA TABLA YA INCLUIDA) --}}
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                    <tr class="text-uppercase" style="font-size: 0.7rem; font-weight: 800; letter-spacing: 0.5px; color: #64748b;">
                        <th class="ps-4 py-3">ID Proveedor</th>
                        <th class="py-3">Nombre y correo</th>
                        <th class="py-3">Perfil / Rol</th>
                        <th class="py-3">Alta en Sistema</th>
                        <th class="text-end pe-4 py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Usamos $this->users en lugar de $users --}}
                    @forelse ($this->users as $user)
                        <tr class="log-row">
                            <td class="ps-4">
                                <span class="font-monospace text-muted small">#{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</span>
                            </td>
                            <td>
                                <div class="fw-bold text-main">{{ $user->name }}</div>
                                <div class="x-small text-muted">{{ $user->email }}</div>
                            </td>
                            <td>
                                <span class="status-indicator {{ $user->role == 'admin' ? 'status-error' : 'status-upload' }} d-inline-flex">
                                    <span class="dot"></span> {{ strtoupper($user->role) }}
                                </span>
                            </td>
                            <td>
                                <span class="text-main small fw-bold">{{ $user->created_at->format('M d, Y') }}</span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-light border rounded-3 text-primary shadow-sm" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline form-eliminar-usuario">
                                        @csrf 
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light border rounded-3 text-danger shadow-sm" title="Revocar Acceso">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-5 text-muted">No se encontraron usuarios.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-4 d-flex justify-content-center">
        {{ $this->users->links('pagination::bootstrap-5') }}
    </div>

    {{-- SweetAlert blindado contra recargas reactivas --}}
    <script>
        document.addEventListener('livewire:navigated', () => { initSweetAlertUser(); });
        document.addEventListener('livewire:load', () => { initSweetAlertUser(); });
        document.addEventListener('livewire:update', () => { initSweetAlertUser(); });

        function initSweetAlertUser() {
            const formulariosEliminar = document.querySelectorAll('.form-eliminar-usuario');
            formulariosEliminar.forEach(formulario => {
                formulario.removeEventListener('submit', handleFormSubmitUser);
                formulario.addEventListener('submit', handleFormSubmitUser);
            });
        }

        function handleFormSubmitUser(e) {
            e.preventDefault();
            Swal.fire({
                title: '¿Revocar Acceso?',
                text: "¡El usuario será eliminado permanentemente del sistema!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f43f5e',
                cancelButtonColor: '#64748b',
                confirmButtonText: '<i class="fas fa-user-times me-1"></i> Sí, eliminar',
                cancelButtonText: 'Cancelar',
                customClass: { confirmButton: 'btn btn-danger px-4 rounded-pill', cancelButton: 'btn btn-secondary px-4 rounded-pill me-2' },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) { this.submit(); }
            });
        }
        initSweetAlertUser();
    </script>
</div>