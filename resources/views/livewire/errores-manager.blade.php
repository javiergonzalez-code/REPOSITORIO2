<?php
use function Livewire\Volt\{state, computed, usesPagination};
use App\Models\Log;
use App\Models\User;

usesPagination(theme: 'bootstrap');

// Variables de estado para los filtros
state(['search' => '', 'userFilter' => '', 'nivel' => '', 'fecha' => '']);

// Verificar si es proveedor por seguridad
$esProveedor = computed(function () {
    $user = auth()->user();
    return $user->hasRole('proveedor') || $user->role === 'proveedor';
});

// Sugerencias para el dropdown flotante de usuarios
$sugerencias_usuarios = computed(function () {
    if ($this->esProveedor) {
        return collect();
    }

    $query = User::select('CardName');

    if (strlen($this->userFilter) > 0) {
        $query->where('CardName', 'like', "%{$this->userFilter}%");
    }

    $sugerencias = $query->orderBy('CardName', 'asc')->take(50)->get();

    if (strlen($this->userFilter) > 0 && $sugerencias->count() === 1 && strtolower($sugerencias->first()->CardName) === strtolower($this->userFilter)) {
        return collect();
    }

    return $sugerencias;
});

// Consulta principal reactiva
$errores = computed(function () {
    $user = auth()->user();
    
    // Filtramos para traer los ERRORES y los fallos de SISTEMA
    $query = Log::with('user')->whereIn('modulo', ['ERRORES', 'SISTEMA']);

    if ($this->esProveedor) {
        $query->where('user_id', $user->CardCode);
    }

    // Filtro de búsqueda libre en el detalle
    if ($this->search) {
        $query->where('accion', 'like', "%{$this->search}%");
    }

    // Filtro por usuario
    if ($this->userFilter && !$this->esProveedor) {
        $query->whereHas('user', fn($q) => $q->where('CardName', 'like', "%{$this->userFilter}%"));
    }

    // Filtro por Nivel / Estado (Lógica vinculada a los badges de la tabla)
    if ($this->nivel) {
        $query->where(function ($q) {
            switch ($this->nivel) {
                case 'CRITICO':
                    $q->whereRaw('UPPER(accion) LIKE ?', ['%CRÍTICO%'])
                      ->orWhereRaw('UPPER(accion) LIKE ?', ['%FATAL%'])
                      ->orWhereRaw('UPPER(accion) LIKE ?', ['%SQL%']);
                    break;
                case 'ADVERTENCIA':
                    $q->whereRaw('UPPER(accion) LIKE ?', ['%VALIDACIÓN%'])
                      ->orWhereRaw('UPPER(accion) LIKE ?', ['%LEVE%'])
                      ->orWhereRaw('UPPER(accion) LIKE ?', ['%FORMATO%']);
                    break;
                case 'ACCESO':
                    $q->whereRaw('UPPER(accion) LIKE ?', ['%LOGIN%'])
                      ->orWhereRaw('UPPER(accion) LIKE ?', ['%SESIÓN%'])
                      ->orWhereRaw('UPPER(accion) LIKE ?', ['%CREDENCIALES%']);
                    break;
                case 'SISTEMA':
                    $q->whereRaw('UPPER(accion) NOT LIKE ?', ['%LOGIN%'])
                      ->whereRaw('UPPER(accion) NOT LIKE ?', ['%CRÍTICO%'])
                      ->whereRaw('UPPER(accion) NOT LIKE ?', ['%VALIDACIÓN%']);
                    break;
            }
        });
    }

    // Filtro por fecha exacta
    if ($this->fecha) {
        $query->whereDate('created_at', $this->fecha);
    }

    return $query->latest()->paginate(10);
});

// Función para eliminar el error
$deleteError = function ($errorId) {
    if ($this->esProveedor) return;

    $error = Log::whereIn('modulo', ['ERRORES', 'SISTEMA'])->find($errorId);
    if ($error) {
        $error->delete();
    }
};
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-11">

            <x-module-header icon="fas fa-bug" title="REGISTRO DE ERRORES" subtitle="MÓDULO DE MONITOREO DEL SISTEMA" />

            <div class="mb-3 text-end" style="font-size: 0.8rem; color: #dc3545; font-weight: 700;">
                <i class="fas fa-exclamation-circle me-1"></i> {{ $this->errores->total() }} EXCEPCIONES ENCONTRADAS
            </div>

            {{-- FILTROS --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white custom-card" style="overflow: visible; z-index: 1050;">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h6 class="text-uppercase fw-black mb-0 text-muted" style="font-size: 1rem; letter-spacing: 1px;">
                        <i class="fas fa-filter me-2"></i> Filtros de búsqueda
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3 align-items-end">

                        {{-- Búsqueda Libre --}}
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-custom text-uppercase x-small fw-bold">Detalle del Error</label>
                            <div class="position-relative">
                                <i class="fas fa-search text-muted position-absolute top-50 start-0 translate-middle-y ms-3"></i>
                                <input type="text" wire:model.live.debounce.300ms="search" class="form-control ps-5" placeholder="Buscar texto...">
                            </div>
                        </div>

                        {{-- Filtro de Usuario --}}
                        @if (!$this->esProveedor)
                            <div class="col-lg-3 col-md-6" x-data="{ showDropdown: false }" @click.outside="showDropdown = false">
                                <label class="form-label-custom text-uppercase x-small fw-bold">Usuario Afectado</label>
                                <div style="position: relative !important;">
                                    <i class="fas fa-user text-muted position-absolute top-50 start-0 translate-middle-y ms-3" style="z-index: 10;"></i>
                                    <input type="text" wire:model.live.debounce.300ms="userFilter" class="form-control ps-5"
                                        placeholder="Seleccionar usuario..." autocomplete="off" @focus="showDropdown = true" @input="showDropdown = true">

                                    @if (count($this->sugerencias_usuarios) > 0)
                                        <div class="w-100 border rounded-3 shadow-lg" x-show="showDropdown" x-transition.opacity
                                            style="display: none; position: absolute !important; top: 100% !important; left: 0 !important; margin-top: 5px !important; z-index: 10000 !important; overflow-y: auto; max-height: 250px; background-color: #ffffff !important;">
                                            <ul class="list-unstyled mb-0">
                                                @foreach ($this->sugerencias_usuarios as $sugerencia)
                                                    <li>
                                                        <button type="button" class="w-100 border-0 text-start px-3 py-2"
                                                            style="font-size: 0.9rem; background-color: transparent; color: #1e293b;"
                                                            wire:click="$set('userFilter', '{{ $sugerencia->CardName }}')"
                                                            @click="showDropdown = false"
                                                            onmouseover="this.style.backgroundColor='#f1f5f9'"
                                                            onmouseout="this.style.backgroundColor='transparent'">
                                                            <i class="fas fa-user-circle text-danger me-2"></i> {{ $sugerencia->CardName }}
                                                        </button>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        {{-- Filtro de Nivel / Estado --}}
                        <div class="col-lg-2 col-md-6">
                            <label class="form-label-custom text-uppercase x-small fw-bold">Nivel / Estado</label>
                            <select wire:model.live="nivel" class="form-control">
                                <option value="">Todos los niveles</option>
                                <option value="CRITICO">Error Crítico</option>
                                <option value="ADVERTENCIA">Advertencia</option>
                                <option value="SISTEMA">Error de Sistema</option>
                            </select>
                        </div>

                        {{-- Fecha --}}
                        <div class="col-lg-2 col-md-6">
                            <label class="form-label-custom text-uppercase x-small fw-bold">Fecha</label>
                            <input type="date" wire:model.live="fecha" class="form-control">
                        </div>

                        {{-- Botón Limpiar --}}
                        <div class="{{ $this->esProveedor ? 'col-lg-5' : 'col-lg-2' }} col-md-12">
                            <button wire:click="$set('search', ''); $set('userFilter', ''); $set('nivel', ''); $set('fecha', '')"
                                class="btn btn-outline-secondary rounded-pill w-100 fw-bold">
                                <i class="fas fa-eraser me-1"></i> Limpiar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TABLA --}}
            <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden custom-card">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2">
                    <h6 class="text-uppercase fw-bold mb-0 text-danger" style="font-size: 0.9rem; letter-spacing: 1px;">
                        <i class="fas fa-exclamation-triangle me-2"></i> Excepciones Detectadas
                    </h6>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 border-top-0">
                            <thead style="background: #fff5f5;">
                                <tr class="text-muted text-uppercase" style="font-size: 0.85rem; letter-spacing: 0.5px;">
                                    <th class="ps-4 py-3 border-0 rounded-start">Nivel / Estado</th>
                                    <th class="py-3 border-0">Usuario Afectado</th>
                                    <th class="py-3 border-0">Detalle del Error</th>
                                    <th class="text-center py-3 border-0">Fecha y Hora</th>
                                    <th class="text-center py-3 pe-4 border-0 rounded-end">Acciones</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($this->errores as $error)
                                    @php
                                        $textoMostrar = $error->accion;
                                        $accionUpper = strtoupper($textoMostrar);

                                        // Lógica de Estilos (Debe coincidir con el Filtro)
                                        $badgeStyle = 'status-general';
                                        $badgeText  = 'ERROR DE SISTEMA';

                                        if (str_contains($accionUpper, 'LOGIN') || str_contains($accionUpper, 'SESIÓN') || str_contains($accionUpper, 'CREDENCIALES')) {
                                            $badgeStyle = 'status-auth';
                                            $badgeText  = 'FALLO DE ACCESO';
                                        } elseif (str_contains($accionUpper, 'VALIDACIÓN') || str_contains($accionUpper, 'LEVE') || str_contains($accionUpper, 'FORMATO')) {
                                            $badgeStyle = 'status-warning';
                                            $badgeText  = 'ADVERTENCIA';
                                        } elseif (str_contains($accionUpper, 'CRÍTICO') || str_contains($accionUpper, 'FATAL') || str_contains($accionUpper, 'SQL')) {
                                            $badgeStyle = 'status-error';
                                            $badgeText  = 'ERROR CRÍTICO';
                                        }

                                        if ($this->esProveedor) {
                                            if (str_contains($textoMostrar, 'CRÍTICO') || str_contains($textoMostrar, 'Leve') || str_contains($textoMostrar, 'Validación/Seguridad')) {
                                                $textoMostrar = 'Error: El formato o contenido del archivo no está permitido.';
                                                $badgeStyle = 'status-warning';
                                                $badgeText  = 'CARGA BLOQUEADA';
                                            } elseif (str_contains($textoMostrar, 'IP:')) {
                                                $textoMostrar = preg_replace('/\| IP: [0-9\.]+/', '', $textoMostrar);
                                            }
                                        }
                                    @endphp
                                    <tr>
                                        <td class="ps-4 py-3">
                                            <div class="status-indicator {{ $badgeStyle }}">
                                                <span class="dot"></span> {{ $badgeText }}
                                            </div>
                                        </td>
                                        
                                        <td class="py-3">
                                            <div class="d-flex align-items-center gap-3">
                                                @if ($error->user)
                                                    <div class="rounded-circle bg-danger bg-gradient text-white d-flex align-items-center justify-content-center fw-bold shadow-sm"
                                                        style="width: 38px; height: 38px;">
                                                        {{ strtoupper(substr($error->user->CardName ?? 'U', 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <span class="d-block fw-bold mb-0" style="font-size: 0.9rem;">
                                                            {{ $error->user->CardName ?? 'Usuario' }}
                                                        </span>
                                                        <span class="text-uppercase" style="font-size: 0.7rem; color: #94a3b8;">
                                                            {{ $error->user->role ?? 'admin' }}
                                                        </span>
                                                    </div>
                                                @else
                                                    <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                                        <i class="fas fa-user-slash"></i>
                                                    </div>
                                                    <span class="fw-bold text-muted" style="font-size: 0.9rem;">Sistema</span>
                                                @endif
                                            </div>
                                        </td>

                                        <td class="py-3">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-white rounded p-2 me-3 shadow-sm border border-danger-subtle">
                                                    <i class="fas fa-exclamation-triangle text-danger"></i>
                                                </div>
                                                <div>
                                                    <span class="d-block text-dark fw-medium text-truncate" style="max-width: 250px;" title="{{ $textoMostrar }}">
                                                        {{ $textoMostrar }}
                                                    </span>
                                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle mt-1">
                                                        Módulo: {{ $error->modulo }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="text-center py-3">
                                            <span class="d-block fw-bold text-dark" style="font-size: 0.85rem;">{{ $error->created_at->format('d/m/Y') }}</span>
                                            <span class="text-muted font-monospace small">{{ $error->created_at->format('h:i A') }}</span>
                                        </td>

                                        <td class="text-center py-3 pe-4">
                                            <div class="d-flex justify-content-center gap-2">
                                                <a href="{{ route('errores.show', $error->id) }}" class="btn btn-sm btn-outline-primary rounded-circle">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                @if (!$this->esProveedor)
                                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-circle" 
                                                        @click="/* Tu lógica de SweetAlert aquí */ $wire.deleteError({{ $error->id }})">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <i class="fas fa-check-circle fs-1 text-success opacity-50 mb-3"></i>
                                            <h5 class="fw-bold">No hay errores con estos filtros</h5>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="mt-4 d-flex justify-content-center">
                {{ $this->errores->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>
</div>