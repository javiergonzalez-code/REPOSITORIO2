<?php
use function Livewire\Volt\{state, computed, usesPagination};
use App\Models\Archivo;
use App\Models\User;

usesPagination(theme: 'bootstrap');

// Variables de estado
state(['search' => '', 'userFilter' => '', 'extension' => '', 'fecha' => '']);

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

// Computar la consulta principal reactiva
$ordenes = computed(function () {
    $user = auth()->user();
    $query = Archivo::with('user')->where('modulo', 'OC');

    if ($this->esProveedor) {
        $query->where('user_id', $user->id);
    }

    if ($this->search) {
        $query->where('nombre_original', 'like', "%{$this->search}%");
    }
    
    if ($this->userFilter && !$this->esProveedor) {
        $query->whereHas('user', fn($q) => $q->where('name', 'like', "%{$this->userFilter}%"));
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
    {{-- Indicador de total de registros --}}
    <div class="mb-3 text-end" style="font-size: 0.8rem; color: #64748b; font-weight: 700;">
        {{ $this->ordenes->total() }} DOCUMENTOS ENCONTRADOS
    </div>

    {{-- RECUADRO 2: FILTROS --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white custom-card">
        <div class="card-header bg-transparent border-0 pt-4 px-4">
            <h6 class="text-uppercase fw-black mb-0 text-muted" style="font-size: 1rem; letter-spacing: 1px;">
                <i class="fas fa-filter me-2"></i> Filtros de búsqueda
            </h6>
        </div>
        <div class="card-body p-4">
            <div class="row g-3 align-items-end">
                
                {{-- Búsqueda Libre --}}
                <div class="col-lg-3 col-md-6">
                    <label class="form-label-custom text-uppercase x-small fw-bold">Búsqueda General</label>
                    <div class="position-relative">
                        <i class="fas fa-search text-muted position-absolute top-50 start-0 translate-middle-y ms-3"></i>
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control ps-5" placeholder="Nombre del archivo...">
                    </div>
                </div>

                {{-- Filtro de Usuario con Dropdown (Oculto para Proveedores) --}}
                @if(!$this->esProveedor)
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label-custom text-uppercase x-small fw-bold">Cargado por</label>
                        <div class="position-relative">
                            <input type="text" wire:model.live.debounce.300ms="userFilter" class="form-control" placeholder="Escribir usuario..." autocomplete="off">
                            
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

                {{-- Botón Limpiar --}}
                <div class="{{ $this->esProveedor ? 'col-lg-5' : 'col-lg-2' }} col-md-12">
                    <div class="d-flex gap-2">
                        <button wire:click="$set('search', ''); $set('userFilter', ''); $set('extension', ''); $set('fecha', '')" class="btn btn-outline-secondary rounded-pill w-100 fw-bold">
                            <i class="fas fa-eraser me-1"></i> Limpiar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- RECUADRO 3: TABLA --}}
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden custom-card">
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
                                <div class="d-flex align-items-center">
                                    <div class="avatar-pill bg-blue-ragon-gradient shadow-sm">
                                        {{ strtoupper(substr($oc->user->name ?? '?', 0, 1)) }}
                                    </div>
                                    <div class="ms-3">
                                        <div class="fw-bold text-dark mb-0">{{ $oc->user->name }}</div>
                                        <div class="x-small text-muted">ID: #{{ str_pad($oc->user->id, 4, '0', STR_PAD_LEFT) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @php $ext = pathinfo($oc->nombre_original, PATHINFO_EXTENSION); @endphp
                                    <i class="fas {{ strtolower($ext) == 'csv' ? 'fa-file-csv text-success' : 'fa-file-code text-info' }} fs-5 me-2"></i>
                                    <span class="text-primary fw-semibold small">{{ $oc->nombre_original }}</span>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="fw-bold d-block text-dark mb-0 small">{{ $oc->created_at->format('d/m/Y') }}</span>
                                <span class="text-muted font-monospace small">{{ $oc->created_at->format('H:i') }} hrs</span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('oc.preview', $oc->id) }}" class="action-btn btn-view" title="Ver"><i class="fas fa-eye"></i></a>
                                    <a href="{{ route('archivos.download', $oc->id) }}" class="action-btn btn-download" title="Descargar"><i class="fas fa-cloud-download-alt"></i></a>
                                    
                                    <form action="{{ route('oc.destroy', $oc->id) }}" method="POST" class="d-inline form-eliminar-oc">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn btn-delete" title="Eliminar">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-5 text-muted">No se encontraron archivos.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-4 d-flex justify-content-center">
        {{ $this->ordenes->links('pagination::bootstrap-5') }}
    </div>

    {{-- Estilos y Script de SweetAlert --}}
    <style>
        .action-btn { width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; border-radius: 8px; transition: all 0.2s ease; border: none; text-decoration: none; font-size: 0.9rem; }
        .btn-view { background-color: #f1f5f9; color: #64748b; } .btn-view:hover { background-color: #e2e8f0; transform: translateY(-2px); }
        .btn-download { background-color: #ecfdf5; color: #10b981; } .btn-download:hover { background-color: #10b981; color: white; transform: translateY(-2px); }
        .btn-delete { background-color: #fff1f2; color: #f43f5e; } .btn-delete:hover { background-color: #f43f5e; color: white; transform: translateY(-2px); }
        .avatar-pill { width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 0.8rem; }
        .bg-blue-ragon-gradient { background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%); }
    </style>

    <script>
        document.addEventListener('livewire:navigated', () => { initSweetAlertOC(); });
        document.addEventListener('livewire:load', () => { initSweetAlertOC(); });
        document.addEventListener('livewire:update', () => { initSweetAlertOC(); });

        function initSweetAlertOC() {
            const formulariosEliminar = document.querySelectorAll('.form-eliminar-oc');
            formulariosEliminar.forEach(formulario => {
                formulario.removeEventListener('submit', handleFormSubmitOC);
                formulario.addEventListener('submit', handleFormSubmitOC);
            });
        }

        function handleFormSubmitOC(e) {
            e.preventDefault();
            Swal.fire({
                title: '¿Estás seguro?',
                text: "¡El archivo será eliminado permanentemente del sistema!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f43f5e',
                cancelButtonColor: '#64748b',
                confirmButtonText: '<i class="fas fa-trash-alt me-1"></i> Sí, eliminar',
                cancelButtonText: 'Cancelar',
                customClass: { confirmButton: 'btn btn-danger px-4 rounded-pill', cancelButton: 'btn btn-secondary px-4 rounded-pill me-2' },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) { this.submit(); }
            });
        }
        initSweetAlertOC();
    </script>
</div>