<?php
use function Livewire\Volt\{state, computed, usesPagination};
use App\Models\Archivo;
use App\Models\User;

usesPagination(theme: 'bootstrap');

state(['search' => '', 'userFilter' => '', 'extension' => '', 'fecha' => '']);

$esProveedor = computed(function () {
    $user = auth()->user();
    return $user->hasRole('proveedor') || $user->role === 'proveedor';
});

$sugerencias_usuarios = computed(function () {
    if ($this->esProveedor || strlen($this->userFilter) < 1) {
        return collect();
    }

    $sugerencias = User::select('CardName')
        ->where('CardName', 'like', "%{$this->userFilter}%")
        ->orderBy('CardName', 'asc')
        ->take(6)
        ->get();

    if ($sugerencias->count() === 1 && strtolower($sugerencias->first()->CardName) === strtolower($this->userFilter)) {
        return collect();
    }

    return $sugerencias;
});

$ordenes = computed(function () {
    $user = auth()->user();
    $query = Archivo::with('user')->whereIn('modulo', ['OC', 'INPUTS']);
    if ($this->esProveedor) {
        // 3. CAMBIO: Usar CardCode en vez de id
        $query->where('user_id', $user->CardCode);
    }

    if ($this->search) {
        $query->where('nombre_original', 'like', "%{$this->search}%");
    }

    if ($this->userFilter && !$this->esProveedor) {
        // 4. CAMBIO: Filtrar la relación por CardName
        $query->whereHas('user', fn($q) => $q->where('CardName', 'like', "%{$this->userFilter}%"));
    }

    if ($this->extension) {
        $query->where('tipo_archivo', $this->extension);
    }

    if ($this->fecha) {
        $query->whereDate('created_at', $this->fecha);
    }

    return $query->latest()->paginate(10);
});
?>

<div>

    <div class="mb-3 text-end" style="font-size: 0.8rem; color: #64748b; font-weight: 700;">
        {{ $this->ordenes->total() }} DOCUMENTOS ENCONTRADOS
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white custom-card"
        style="overflow: visible; position: relative; z-index: 1050;">
        <div class="card-header bg-transparent border-0 pt-4 px-4">
            <h6 class="text-uppercase fw-black mb-0 text-muted" style="font-size: 1rem; letter-spacing: 1px;">
                <i class="fas fa-filter me-2"></i> Filtros de búsqueda
            </h6>
        </div>
        <div class="card-body p-4">
            <div class="row g-3 align-items-end">

                <div class="col-lg-3 col-md-6">
                    <label class="form-label-custom text-uppercase x-small fw-bold">Búsqueda General</label>
                    <div class="position-relative">
                        <i
                            class="fas fa-search text-muted position-absolute top-50 start-0 translate-middle-y ms-3"></i>
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control ps-5"
                            placeholder="Nombre del archivo...">
                    </div>
                </div>

                @if (!$this->esProveedor)
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label-custom text-uppercase x-small fw-bold">Cargado por</label>
                        <div style="position: relative !important;">
                            <i class="fas fa-user text-muted position-absolute top-50 start-0 translate-middle-y ms-3"
                                style="z-index: 10;"></i>
                            <input type="text" wire:model.live.debounce.300ms="userFilter" class="form-control ps-5"
                                placeholder="Escribir nombre..." autocomplete="off">

                            {{-- LISTA DESPLEGABLE FLOTANTE CON FONDO BLANCO FORZADO --}}
                            @if (count($this->sugerencias_usuarios) > 0)
                                <div class="w-100 border rounded-3 shadow-lg"
                                    style="position: absolute !important; top: 100% !important; left: 0 !important; margin-top: 5px !important; z-index: 10000 !important; overflow: hidden; display: block !important; background-color: #ffffff !important;">
                                    <ul class="list-unstyled mb-0">
                                        @foreach ($this->sugerencias_usuarios as $sugerencia)
                                            <li>
                                                {{-- 5. CAMBIO: Mostrar CardName al dar click en sugerencias --}}
                                                <button type="button" class="w-100 border-0 text-start px-3 py-2"
                                                    style="font-size: 0.9rem; background-color: transparent; color: #1e293b; transition: all 0.2s;"
                                                    wire:click="$set('userFilter', '{{ $sugerencia->CardName }}')"
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

                {{-- Extensión --}}
                <div class="col-lg-2 col-md-6">
                    <label class="form-label-custom text-uppercase x-small fw-bold">Extensión</label>
                    <select wire:model.live="extension" class="form-control">
                        <option value="">Todas</option>
                        <option value="csv">CSV</option>
                        <option value="xml">XML</option>
                    </select>
                </div>

                {{-- Fecha --}}
                <div class="col-lg-2 col-md-6">
                    <label class="form-label-custom text-uppercase x-small fw-bold">Fecha</label>
                    <input type="date" wire:model.live="fecha" class="form-control">
                </div>

                <div class="{{ $this->esProveedor ? 'col-lg-5' : 'col-lg-2' }} col-md-12">
                    <button
                        wire:click="$set('search', ''); $set('userFilter', ''); $set('extension', ''); $set('fecha', '')"
                        class="btn btn-outline-secondary rounded-pill w-100 fw-bold">
                        <i class="fas fa-eraser me-1"></i> Limpiar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white custom-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                    <tr class="text-uppercase" style="font-size: 0.7rem; font-weight: 800; color: #64748b;">
                        <th class="ps-4 py-3">Estado</th>
                        <th class="py-3">Proveedor / Usuario</th>
                        <th class="py-3">Archivo</th>
                        <th class="text-center py-3">Fecha de Carga</th>
                        <th class="text-end pe-4 py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($this->ordenes as $oc)
                        <tr class="log-row">
                            <td class="ps-4">
                                <span class="status-indicator status-upload d-inline-flex">
                                    <span class="dot"></span> RECIBIDO
                                </span>
                            </td>
                            <td>
                                {{-- 6. CAMBIO CRÍTICO: Mandar CardName y CardCode al componente avatar --}}
                                <x-user-avatar :user="$oc->user" />
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @php $ext = pathinfo($oc->nombre_original, PATHINFO_EXTENSION); @endphp
                                    <i
                                        class="fas {{ strtolower($ext) == 'csv' ? 'fa-file-csv text-success' : 'fa-file-code text-info' }} fs-5 me-2"></i>
                                    <span class="text-primary fw-semibold small">{{ $oc->nombre_original }}</span>
                                </div>
                            </td>
                            <td class="text-center">
                                <span
                                    class="fw-bold d-block text-dark mb-0 small">{{ $oc->created_at->format('d/m/Y') }}</span>
                                <span class="text-muted font-monospace small">{{ $oc->created_at->format('H:i') }}
                                    hrs</span>
                            </td>
                            <td class="text-end pe-4">
                                <x-table-actions viewRoute="{{ route('oc.preview', $oc->id) }}"
                                    downloadRoute="{{ route('oc.download', $oc->id) }}"
                                    deleteRoute="{{ route('oc.destroy', $oc->id) }}" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">No se encontraron archivos.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4 d-flex justify-content-center">
        {{ $this->ordenes->links('pagination::bootstrap-5') }}
    </div>

    <x-delete-confirm-script />

</div>
