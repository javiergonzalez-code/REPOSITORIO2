@props(['oc', 'extension'])

<div class="card border-0 shadow-sm rounded-4 mb-4 custom-card">
    <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div class="d-flex align-items-center">
            <div class="header-icon-box shadow-sm me-3"
                style="width: 55px; height: 55px; display: flex; align-items: center; justify-content: center; border-radius: 12px;">
                <i class="fas {{ in_array($extension, ['xlsx', 'xls', 'csv']) ? 'fa-file-excel' : 'fa-file-code' }} fa-lg"></i>
            </div>
            <div>
                <h2 class="audit-title mb-0" style="font-size: 1.4rem; font-weight: 800; letter-spacing: -0.5px;">
                    {{ $oc->nombre_original }}
                </h2>
                <div class="audit-subtitle d-flex align-items-center" style="font-size: 0.9rem; font-weight: 700;">
                    <span class="badge bg-primary-light text-primary border-primary-subtle px-2 py-1 rounded-pill me-2 text-uppercase">
                        Formato: {{ $extension }}
                    </span>
                    <span class="divider-v mx-2"></span>
                    <i class="fas fa-user-circle me-1"></i> 
                    <span class="text-main">Subido por: {{ $oc->user->name }}</span>
                </div>
            </div>
        </div>
        
        <div class="header-actions d-flex align-items-center gap-2">
            <a href="{{ route('oc.index') }}" class="btn-ragon-outline shadow-sm">
                <i class="fas fa-arrow-left me-2"></i> VOLVER
            </a>
            
            <a href="{{ route('archivos.download', $oc->id) }}" class="btn btn-gradient rounded-pill">
                <i class="fas fa-download me-2"></i> DESCARGAR
            </a>

            {{-- OJO AQUÍ: Usamos la clase form-eliminar-registro del componente global --}}
            <form action="{{ route('oc.destroy', $oc->id) }}" method="POST" class="m-0 p-0 form-eliminar-registro">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger rounded-pill shadow-sm">
                    <i class="fas fa-trash-alt me-2"></i> ELIMINAR
                </button>
            </form>
        </div>
    </div>
</div>