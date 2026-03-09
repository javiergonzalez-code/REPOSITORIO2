<?php
use function Livewire\Volt\{state, computed, usesPagination};
use App\Models\Log;
use App\Models\User;

usesPagination(theme: 'bootstrap');

state(['search' => '', 'userFilter' => '', 'accion' => '', 'fecha' => '']);

$esProveedor = computed(function () {
    $user = auth()->user();
    return $user->hasRole('proveedor') || $user->role === 'proveedor';
});

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

$logs = computed(function () {
    $user = auth()->user();
    $query = Log::with('user');

    if ($this->esProveedor) {
        $query->where('user_id', $user->id);
    }
    if ($this->search) {
        $query->where('descripcion', 'like', "%{$this->search}%");
    }
    if ($this->userFilter && !$this->esProveedor) {
        $query->whereHas('user', fn($q) => $q->where('name', 'like', "%{$this->userFilter}%"));
    }
    if ($this->accion) {
        $query->where('accion', $this->accion);
    }
    if ($this->fecha) {
        $query->whereDate('created_at', $this->fecha);
    }

    return $query->latest()->paginate(10);
});
?>

{{-- REGLA DE ORO DE LIVEWIRE: UN SOLO DIV PADRE QUE ENVUELVE TODO --}}
<div >

    <div class="mb-3 text-end" style="font-size: 0.8rem; color: #64748b; font-weight: 700;">
        {{ $this->logs->total() }} REGISTROS ENCONTRADOS
    </div>

    {{-- RECUADRO 2: FILTROS --}}
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
                    <label class="form-label-custom text-uppercase x-small fw-bold">Descripción</label>
                    <div class="position-relative">
                        <i
                            class="fas fa-search text-muted position-absolute top-50 start-0 translate-middle-y ms-3"></i>
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control ps-5"
                            placeholder="Buscar en logs...">
                    </div>
                </div>

                {{-- Filtro de Usuario con Dropdown Arreglado --}}
                @if (!$this->esProveedor)
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label-custom text-uppercase x-small fw-bold">Usuario</label>
                        <div style="position: relative !important;">
                            <i class="fas fa-user text-muted position-absolute top-50 start-0 translate-middle-y ms-3"
                                style="z-index: 10;"></i>
                            <input type="text" wire:model.live.debounce.300ms="userFilter" class="form-control ps-5"
                                placeholder="Escribir usuario..." autocomplete="off">

                            {{-- LISTA DESPLEGABLE FLOTANTE CON FONDO BLANCO FORZADO --}}
                            @if (count($this->sugerencias_usuarios) > 0)
                                <div class="w-100 border rounded-3 shadow-lg"
                                    style="position: absolute !important; top: 100% !important; left: 0 !important; margin-top: 5px !important; z-index: 10000 !important; overflow: hidden; display: block !important; background-color: #ffffff !important;">
                                    <ul class="list-unstyled mb-0">
                                        @foreach ($this->sugerencias_usuarios as $sugerencia)
                                            <li>
                                                <button type="button" class="w-100 border-0 text-start px-3 py-2"
                                                    style="font-size: 0.9rem; background-color: transparent; color: #1e293b; transition: all 0.2s;"
                                                    wire:click="$set('userFilter', '{{ $sugerencia->name }}')"
                                                    onmouseover="this.style.backgroundColor='#f1f5f9'"
                                                    onmouseout="this.style.backgroundColor='transparent'">
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
                @endif

                {{-- Acción --}}
                <div class="col-lg-2 col-md-6">
                    <label class="form-label-custom text-uppercase x-small fw-bold">Acción</label>
                    <select wire:model.live="accion" class="form-control">
                        <option value="">Todas</option>
                        <option value="CARGA">Carga</option>
                        <option value="DESCARGA">Descarga</option>
                        <option value="ELIMINACION">Eliminación</option>
                        <option value="LOGIN">Login</option>
                        <option value="LOGOUT">Logout</option>
                    </select>
                </div>

                {{-- Fecha --}}
                <div class="col-lg-2 col-md-6">
                    <label class="form-label-custom text-uppercase x-small fw-bold">Fecha</label>
                    <input type="date" wire:model.live="fecha" class="form-control">
                </div>

                {{-- Botón Limpiar --}}
                <div class="{{ $this->esProveedor ? 'col-lg-5' : 'col-lg-2' }} col-md-12">
                    <button
                        wire:click="$set('search', ''); $set('userFilter', ''); $set('accion', ''); $set('fecha', '')"
                        class="btn btn-outline-secondary rounded-pill w-100 fw-bold">
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
                    <tr class="text-uppercase"
                        style="font-size: 0.7rem; font-weight: 800;letter-spacing: 0.5px; color: #64748b;">
                        <th class="ps-4 py-3 border-0">Operador</th>
                        <th class="text-center py-3 border-0">Actividad</th>
                        <th class="py-3 border-0">Módulo</th>
                        <th class="text-center py-3 border-0">Fecha y Hora</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($this->logs as $log)
                        @php
                            $badgeStyle = match (true) {
                                str_contains(strtoupper($log->accion), 'LOGIN') ||
                                    str_contains(strtoupper($log->accion), 'SESIÓN')
                                    => 'status-upload',
                                str_contains(strtoupper($log->accion), 'BORRADO') ||
                                    str_contains(strtoupper($log->accion), 'ELIMINÓ')
                                    => 'status-error',
                                default => 'status-general',
                            };
                        @endphp
                        <tr class="log-row">
                            <td class="ps-4 py-3">
                                <x-user-avatar :name="$log->user->name ?? 'Sistema'" :userId="$log->user->id ?? 0" :subtitle="$log->user->role ?? 'N/A'" />
                            </td>
                            <td class="text-center py-3">
                                <div class="status-indicator {{ $badgeStyle }}">
                                    <span class="dot"></span> {{ $log->accion }}
                                </div>
                            </td>
                            <td class="py-3">
                                <span class="badge-outline text-muted fw-bold"
                                    style="border: 1px solid #e2e8f0; padding: 6px 12px; border-radius: 6px; font-size: 0.75rem;">
                                    <i class="fas fa-cube me-1"></i> {{ $log->modulo ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="text-center py-3">
                                <span class="fw-bold d-block text-main mb-0"
                                    style="font-size: 0.85rem;">{{ $log->created_at->format('d/m/Y') }}</span>
                                <span
                                    class="x-small text-muted font-monospace">{{ $log->created_at->format('h:i A') }}</span>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted fw-bold">No se encontraron logs con
                                estos filtros.</td>
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
