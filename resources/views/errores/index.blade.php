@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-11">
                
                {{-- RECUADRO 1: TÍTULO (Usando nuestro componente reutilizable) --}}
                <x-module-header 
                    icon="fas fa-bug" 
                    title="REGISTRO DE ERRORES" 
                    subtitle="MÓDULO DE MONITOREO DEL SISTEMA"
                />

                {{-- RECUADRO 2: TABLA DE ERRORES (DISEÑO PREMIUM) --}}
                <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden custom-card">
                    
                    {{-- Encabezado de la tabla --}}
                    <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2 d-flex justify-content-between align-items-center">
                        <h6 class="text-uppercase fw-black mb-0 text-danger" style="font-size: 0.9rem; letter-spacing: 1px;">
                            <i class="fas fa-exclamation-triangle me-2"></i> Excepciones Detectadas
                        </h6>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 border-top-0">
                                <thead style="background: #f8fafc;">
                                    <tr class="text-uppercase" style="font-size: 0.7rem; font-weight: 700; color: #64748b; letter-spacing: 0.5px;">
                                        <th class="ps-4 py-3 border-0">Nivel / Estado</th>
                                        <th class="py-3 border-0">Usuario Afectado</th>
                                        <th class="py-3 border-0">Detalle del Error</th>
                                        <th class="text-center py-3 pe-4 border-0">Fecha y Hora</th>
                                    </tr>
                                </thead>
                                <tbody class="border-top-0">
                                    {{-- Asumiendo que tu controlador pasa la variable $logs o $errores --}}
                                    @forelse($logs ?? $errores as $error)
                                        <tr style="transition: all 0.2s ease;">
                                            
                                            {{-- COLUMNA 1: Nivel de Error --}}
                                            <td class="ps-4 py-3">
                                                <div class="status-indicator status-error">
                                                    <span class="dot"></span> FALLO DEL SISTEMA
                                                </div>
                                            </td>

                                            {{-- COLUMNA 2: Usuario --}}
                                            <td class="py-3">
                                                <x-user-avatar 
                                                    :name="$error->user->name ?? 'Desconocido'" 
                                                    :userId="$error->user->id ?? 0" 
                                                    :subtitle="$error->user->role ?? 'SISTEMA'" 
                                                />
                                            </td>

                                            {{-- COLUMNA 3: Descripción del Error --}}
                                            <td class="py-3" style="max-width: 320px;">
                                                <div class="text-danger fw-bold mb-1" style="font-size: 0.85rem; line-height: 1.4;">
                                                    <i class="fas fa-times-circle me-1"></i> {{ $error->descripcion ?? 'Error no especificado' }}
                                                </div>
                                                <div class="d-inline-flex align-items-center rounded bg-light text-secondary px-2 py-1 shadow-sm mt-1" 
                                                     style="font-size: 0.7rem; font-family: monospace; border: 1px solid #e2e8f0;">
                                                    <i class="fas fa-cube me-2" style="opacity: 0.6;"></i> Módulo: {{ $error->modulo ?? 'Global' }}
                                                </div>
                                            </td>

                                            {{-- COLUMNA 4: Fecha y Hora --}}
                                            <td class="text-center py-3 pe-4">
                                                <div class="d-flex flex-column align-items-center justify-content-center">
                                                    <span class="fw-bold text-dark mb-1" style="font-size: 0.85rem;">
                                                        {{ $error->created_at->format('d M, Y') }}
                                                    </span>
                                                    <span class="badge bg-light text-secondary border font-monospace shadow-sm" style="font-size: 0.75rem; padding: 4px 8px;">
                                                        <i class="far fa-clock me-1 text-muted"></i> {{ $error->created_at->format('H:i:s') }}
                                                    </span>
                                                </div>
                                            </td>

                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-5">
                                                <div class="py-4">
                                                    <i class="fas fa-check-circle fa-3x text-success mb-3 opacity-50"></i>
                                                    <h5 class="text-muted fw-bold">Sistema Estable</h5>
                                                    <p class="text-muted small">No se han registrado errores o excepciones recientemente.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                {{-- Paginación (Si aplica) --}}
                @if(isset($logs) && method_exists($logs, 'links'))
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $logs->links('pagination::bootstrap-5') }}
                    </div>
                @elseif(isset($errores) && method_exists($errores, 'links'))
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $errores->links('pagination::bootstrap-5') }}
                    </div>
                @endif

            </div>
        </div>
    </div>
@endsection