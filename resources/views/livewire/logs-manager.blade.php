<?php
use function Livewire\Volt\{state, computed, usesPagination};
use App\Models\Log;
use App\Models\User;

usesPagination(theme: 'bootstrap');

// Variables reactivas
state(['search' => '', 'userFilter' => '', 'accion' => '', 'fecha' => '']);

// Verificar si es proveedor
$esProveedor = computed(function () {
    $user = auth()->user();
    return $user->hasRole('proveedor') || $user->role === 'proveedor';
});

// Lógica inteligente para la lista desplegable de usuarios (Autocompletado)
$sugerencias_usuarios = computed(function () {
    if ($this->esProveedor || strlen($this->userFilter) < 1) {
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

// Consulta reactiva a la BD
$logs = computed(function () {
    $query = Log::with('user');
    
    // Si es proveedor, forzamos a que solo vea sus propios logs
    if ($this->esProveedor) {
        $query->where('user_id', auth()->id());
    }

    // Filtro de Búsqueda Libre
    if ($this->search) {
        $query->where(function ($q) {
            $q->where('accion', 'like', "%{$this->search}%")
              ->orWhere('modulo', 'like', "%{$this->search}%");
        });
    }

    // Filtro por Usuario (Solo para Admins)
    if ($this->userFilter && !$this->esProveedor) {
        $query->whereHas('user', fn($q) => $q->where('name', 'like', "%{$this->userFilter}%"));
    }

    // Filtro por Actividad
    if ($this->accion) {
        $query->where('accion', 'like', "%{$this->accion}%");
    }

    // Filtro por Fecha
    if ($this->fecha) {
        $query->whereDate('created_at', $this->fecha);
    }

    return $query->latest()->paginate(15);
});
?>

<div>
    {{-- Contador reactivo --}}
    <div class="mb-3 text-end" style="font-size: 0.8rem; color: #64748b; font-weight: 700;">
        {{ $this->logs->total() }} REGISTROS ENCONTRADOS
    </div>

    {{-- RECUADRO DE FILTROS --}}
    <div class="card border-0 shadow-sm rounded-4 mb-3 custom-card bg-white">
        <div class="card-header bg-transparent border-0 pt-4 px-4">
            <h6 class="text-uppercase fw-black mb-0 text-muted" style="font-size: 1rem; letter-spacing: 1px;">
                <i class="fas fa-filter me-2"></i> Filtros de búsqueda
            </h6>
        </div>
        <div class="card-body p-4">
            <div class="row g-3 align-items-end">
                
                {{-- 1. Búsqueda Libre --}}
                <div class="col-lg-3 col-md-6">
                    <label class="form-label-custom text-uppercase x-small fw-bold">Palabra Clave</label>
                    <div class="position-relative">
                        <i class="fas fa-search text-muted position-absolute top-50 start-0 translate-middle-y ms-3"></i>
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control ps-5" placeholder="Módulo, acción...">
                    </div>
                </div>

                {{-- 2. Filtro de Usuario con Dropdown (Oculto para Proveedores) --}}
                @if(!$this->esProveedor)
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label-custom text-uppercase x-small fw-bold">Usuario</label>
                        <div class="position-relative">
                            <input type="text" wire:model.live.debounce.300ms="userFilter" class="form-control" placeholder="Escribir nombre..." autocomplete="off">
                            
                            @if(count($this->sugerencias_usuarios) > 0)
                                <div class="position-absolute w-100 bg-white border rounded-3 shadow-lg mt-1 overflow-hidden" style="z-index: 1050;">
                                    <ul class="list-unstyled mb-0">
                                        @foreach($this->sugerencias_usuarios as $sugerencia)
                                            <li>
                                                <button type="button" class="dropdown-item py-2 px-3 text-start w-100 border-0 text-dark" 
                                                    style="background: transparent; font-size: 0.9rem; transition: background 0.2s;"
                                                    wire:click="$set('userFilter', '{{ $sugerencia->name }}')"
                                                    onmouseover="this.style.backgroundColor='#f8fafc'"
                                                    onmouseout="this.style.backgroundColor='transparent'">
                                                    <i class="fas fa-user-circle text-primary me-2"></i> {{ $sugerencia->name }}
                                                </button>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- 3. Filtro por Actividad --}}
                <div class="col-lg-2 col-md-6">
                    <label class="form-label-custom text-uppercase x-small fw-bold">Actividad</label>
                    <select wire:model.live="accion" class="form-control">
                        <option value="">Todas</option>
                        <option value="sesión">Login</option>
                        <option value="SUBIDA">Subida</option>
                        <option value="BORRADO">Borrado</option>
                    </select>
                </div>

                {{-- 4. Filtro por Fecha --}}
                <div class="col-lg-2 col-md-6">
                    <label class="form-label-custom text-uppercase x-small fw-bold">Fecha</label>
                    <input type="date" wire:model.live="fecha" class="form-control">
                </div>

                {{-- 5. Botón Limpiar --}}
                <div class="col-lg-2 col-md-12">
                    <button wire:click="$set('search', ''); $set('userFilter', ''); $set('accion', ''); $set('fecha', '')" class="btn btn-outline-secondary rounded-pill w-100 fw-bold">
                        <i class="fas fa-eraser me-1"></i> Limpiar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- RECUADRO DE TABLA DE DATOS --}}
    <div class="card border-0 shadow-sm rounded-4 custom-card overflow-hidden bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                    <tr class="text-uppercase" style="font-size: 0.7rem; font-weight: 800;letter-spacing: 0.5px; color: #64748b;">
                        <th class="ps-4 py-3">Operador</th>
                        <th class="text-center py-3">Actividad</th>
                        <th class="py-3">Módulo</th>
                        <th class="text-center py-3">Fecha y Hora</th>
                        <th class="text-end pe-4 py-3">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($this->logs as $log)
                        @php
                            $badgeStyle = match (true) {
                                str_contains(strtoupper($log->accion), 'LOGIN') || str_contains(strtoupper($log->accion), 'SESIÓN') => 'status-upload',
                                str_contains(strtoupper($log->accion), 'BORRADO') || str_contains(strtoupper($log->accion), 'ELIMINÓ') => 'status-error',
                                default => 'status-general',
                            };
                        @endphp
                        <tr class="log-row">
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-pill bg-blue-ragon-gradient shadow-sm" style="font-size: 0.8rem; width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                        {{ strtoupper(substr($log->user->name ?? '?', 0, 1)) }}
                                    </div>
                                    <div class="ms-3">
                                        <div class="fw-bold text-main mb-0" style="font-size: 0.9rem;">
                                            {{ $log->user->name ?? 'Sistema' }}
                                        </div>
                                        <div class="x-small text-muted">{{ strtoupper($log->user->role ?? 'N/A') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="status-indicator {{ $badgeStyle }} d-inline-flex">
                                    <span class="dot"></span> {{ $log->accion }}
                                </div>
                            </td>
                            <td>
                                <span class="badge-outline text-muted fw-bold" style="border: 1px solid #e2e8f0; padding: 4px 10px; border-radius: 6px; font-size: 0.75rem;">
                                    <i class="fas fa-cube me-1"></i> {{ $log->modulo }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="fw-bold d-block text-main mb-0" style="font-size: 0.85rem;">{{ $log->created_at->format('d/m/Y') }}</span>
                                <span class="x-small text-muted font-monospace">{{ $log->created_at->format('h:i A') }}</span>
                            </td>
                            <td class="text-end pe-4">
                                <button class="btn btn-sm btn-light border rounded-3 text-primary shadow-sm px-3" style="transition: all 0.2s;">
                                    <i class="fas fa-terminal me-2"></i> Ver
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted fw-bold">No se encontraron logs con estos filtros.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4 d-flex justify-content-center">
        {{ $this->logs->links('pagination::bootstrap-5') }}
    </div>
</div>