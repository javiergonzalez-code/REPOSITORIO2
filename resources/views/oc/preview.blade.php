@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-11">

                {{-- RECUADRO 1: ENCABEZADO DE PREVISUALIZACIÓN --}}
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
                        
                        {{-- AQUÍ ESTÁN LOS BOTONES --}}
                        <div class="header-actions d-flex align-items-center gap-2">
                            <a href="{{ route('oc.index') }}" class="btn-ragon-outline shadow-sm">
                                <i class="fas fa-arrow-left me-2"></i> VOLVER
                            </a>
                            
                            {{-- NOTA: Validar si tu ruta es oc.download o archivos.download según tu web.php --}}
                            <a href="{{ route('archivos.download', $oc->id) }}" class="btn btn-gradient rounded-pill">
                                <i class="fas fa-download me-2"></i> DESCARGAR
                            </a>

                            {{-- NUEVO BOTÓN DE ELIMINAR --}}
                            <form action="{{ route('oc.destroy', $oc->id) }}" method="POST" class="m-0 p-0">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="btn btn-danger rounded-pill shadow-sm" 
                                        onclick="return confirm('¿Estás seguro de que deseas eliminar esta Orden de Compra? Esta acción no se puede deshacer.');">
                                    <i class="fas fa-trash-alt me-2"></i> ELIMINAR
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- RECUADRO 2: CONTENIDO DEL ARCHIVO --}}
                <div class="card border-0 shadow-sm rounded-4 custom-card overflow-hidden">
                    <div class="card-header bg-transparent border-0 py-3 px-4 d-flex align-items-center border-bottom">
                        <h6 class="text-uppercase fw-black mb-0 text-muted" style="font-size: 0.9rem; letter-spacing: 1px;">
                            <i class="fas fa-table me-2"></i> Lectura de datos del sistema
                        </h6>
                        <div class="ms-auto text-muted fw-bold small">
                            Mostrando contenido procesado
                        </div>
                    </div>
                    <div class="card-body p-0">
                        {{-- Contenedor con scroll personalizado --}}
                        <div class="table-responsive" style="max-height: 650px; overflow-y: auto;">
                            <table class="table table-hover align-middle mb-0">
                                <tbody class="font-monospace" style="font-size: 0.85rem;">
                                    @forelse($data as $index => $row)
                                        <tr class="log-row">
                                            {{-- Indicador de número de fila --}}
                                            <td class="text-center text-muted border-end p-2"
                                                style="width: 50px; font-size: 0.7rem; font-weight: 800; background: rgba(0,0,0,0.02);">
                                                {{ $index + 1 }}
                                            </td>

                                            @foreach ($row as $cell)
                                                <td class="p-3 border-end text-main" style="min-width: 150px">
                                                    {{ is_array($cell) ? json_encode($cell) : $cell }}
                                                </td>
                                            @endforeach
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="text-center py-5">
                                                <div class="py-4">
                                                    <i class="fas fa-database fa-3x text-muted mb-3 opacity-25"></i>
                                                    <p class="text-muted fw-bold">El archivo no contiene datos legibles.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection