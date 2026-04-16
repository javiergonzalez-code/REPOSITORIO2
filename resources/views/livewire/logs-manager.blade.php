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
    if ($this->esProveedor) {
        return collect();
    }

    // 1. CAMBIO: Seleccionamos 'CardName' en vez de 'name'
    $query = User::select('CardName');

    // Filtrar solo si hay algo escrito
    if (strlen($this->userFilter) > 0) {
        $query->where('CardName', 'like', "%{$this->userFilter}%");
    }

    // AUMENTAMOS EL LÍMITE PARA QUE TENGAS SUFICIENTES DATOS PARA HACER SCROLL
    $sugerencias = $query->orderBy('CardName', 'asc')->take(50)->get();

    // Ocultar si hay coincidencia exacta
    if (strlen($this->userFilter) > 0 && $sugerencias->count() === 1 && strtolower($sugerencias->first()->CardName) === strtolower($this->userFilter)) {
        return collect();
    }

    return $sugerencias;
});

$logs = computed(function () {
    $user = auth()->user();
    $query = Log::with('user');

    if ($this->esProveedor) {
        // 2. CAMBIO: Usamos CardCode en vez de id
        $query->where('user_id', $user->CardCode);
    }

    if ($this->search) {
        $query->where(function ($q) {
            $q->where('accion', 'like', "%{$this->search}%")->orWhere('modulo', 'like', "%{$this->search}%");
        });
    }

    if ($this->userFilter && !$this->esProveedor) {
        // 3. CAMBIO: Buscamos por CardName en la relación de Eloquent
        $query->whereHas('user', fn($q) => $q->where('CardName', 'like', "%{$this->userFilter}%"));
    }

    if ($this->accion) {
        $query->where(function ($q) {
            switch (strtoupper($this->accion)) {
                case 'CARGA':
                    $q->whereRaw('LOWER(accion) LIKE ?', ['%subió%'])
                        ->orWhereRaw('LOWER(accion) LIKE ?', ['%subio%'])
                        ->orWhereRaw('LOWER(accion) LIKE ?', ['%carga%']);
                    break;
                case 'DESCARGA':
                    $q->whereRaw('LOWER(accion) LIKE ?', ['%descarg%'])->orWhereRaw('LOWER(accion) LIKE ?', ['%download%']);
                    break;
                case 'ELIMINACION':
                    $q->whereRaw('LOWER(accion) LIKE ?', ['%elimin%'])->orWhereRaw('LOWER(accion) LIKE ?', ['%borrad%']);
                    break;
                case 'LOGIN':
                    $q->whereRaw('LOWER(accion) LIKE ?', ['%inicio%'])->orWhereRaw('LOWER(accion) LIKE ?', ['%login%']);
                    break;
                case 'LOGOUT':
                    $q->whereRaw('LOWER(accion) LIKE ?', ['%cierre%'])->orWhereRaw('LOWER(accion) LIKE ?', ['%logout%']);
                    break;
                default:
                    $q->whereRaw('LOWER(accion) LIKE ?', ['%' . strtolower($this->accion) . '%']);
            }
        });
    }

    if ($this->fecha) {
        $query->whereDate('created_at', $this->fecha);
    }

    return $query->latest()->paginate(10);
});
?>

<div>

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
                    <div class="col-lg-3 col-md-6" x-data="{ showDropdown: false }" @click.outside="showDropdown = false">
                        <label class="form-label-custom text-uppercase x-small fw-bold">Usuario</label>
                        <div style="position: relative !important;">
                            <i class="fas fa-user text-muted position-absolute top-50 start-0 translate-middle-y ms-3"
                                style="z-index: 10;"></i>
                            <input type="text" wire:model.live.debounce.300ms="userFilter" class="form-control ps-5"
                                placeholder="Seleccionar usuario..." autocomplete="off" @focus="showDropdown = true"
                                @input="showDropdown = true">

                            {{-- LISTA DESPLEGABLE FLOTANTE CON FONDO BLANCO FORZADO Y SCROLL --}}
                            @if (count($this->sugerencias_usuarios) > 0)
                                <div class="w-100 border rounded-3 shadow-lg" x-show="showDropdown" x-transition.opacity
                                    style="display: none; position: absolute !important; top: 100% !important; left: 0 !important; margin-top: 5px !important; z-index: 10000 !important; overflow-y: auto; max-height: 250px; background-color: #ffffff !important;">
                                    <ul class="list-unstyled mb-0">
                                        @foreach ($this->sugerencias_usuarios as $sugerencia)
                                            <li>
                                                {{-- 4. CAMBIO: Imprimimos CardName en la lista desplegable --}}
                                                <button type="button" class="w-100 border-0 text-start px-3 py-2"
                                                    style="font-size: 0.9rem; background-color: transparent; color: #1e293b; transition: all 0.2s;"
                                                    wire:click="$set('userFilter', '{{ $sugerencia->CardName }}')"
                                                    @click="showDropdown = false"
                                                    onmouseover="this.style.backgroundColor='#f1f5f9'"
                                                    onmouseout="this.style.backgroundColor='transparent'">
                                                    <i class="fas fa-user-circle text-primary me-2"></i>
                                                    {{ $sugerencia->CardName }}
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

                {{-- Botón Limpiar con Protección Livewire --}}
                <div class="{{ $this->esProveedor ? 'col-lg-5' : 'col-lg-2' }} col-md-12">
                    <button
                        wire:click="$set('search', ''); $set('userFilter', ''); $set('accion', ''); $set('fecha', '')"
                        wire:loading.attr="disabled" class="btn btn-outline-secondary rounded-pill w-100 fw-bold">

                        <span wire:loading.remove wire:target="$set">
                            <i class="fas fa-eraser me-1"></i> Limpiar
                        </span>

                        <span wire:loading wire:target="$set">
                            <span class="spinner-border spinner-border-sm me-1" role="status"
                                aria-hidden="true"></span>...
                        </span>

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
                                {{-- 5. CAMBIO CRÍTICO: Mandamos CardName y CardCode al avatar --}}
                                <x-user-avatar :user="$log->user" />
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
