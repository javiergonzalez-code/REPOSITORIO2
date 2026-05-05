@extends('layouts.app')

@section('content')
    @php
        // 1. VERIFICACIÓN DE ROL
        $usuarioActual = auth()->user();
        $esProveedor = $usuarioActual->hasRole('proveedor') || $usuarioActual->role === 'proveedor';

        // 2. VARIABLES INICIALES
        $textoMostrar = $log->accion;
        $accionUpper = strtoupper($log->accion);

        // Clases por defecto (para acciones normales)
        $bordeColor = 'border-primary';
        $textoColor = 'text-dark fw-semibold';

        // 3. LÓGICA DE ENMASCARAMIENTO Y COLORES
        if ($esProveedor) {
            // 🛡️ VISTA PROVEEDOR: Texto censurado y un solo color genérico para errores
            if (
                str_contains($textoMostrar, 'CRÍTICO') ||
                str_contains($textoMostrar, 'Leve') ||
                str_contains($textoMostrar, 'Validación/Seguridad')
            ) {
                $textoMostrar = 'Carga bloqueada: El formato o contenido del archivo no está permitido.';
                $bordeColor = 'border-danger'; // Error genérico rojo
            } elseif (str_contains($textoMostrar, 'IP:')) {
                $textoMostrar = preg_replace('/\| IP: [0-9\.]+/', '', $textoMostrar);
            }

            // Si es un borrado normal, también le ponemos borde rojo (pero sin revelar amenazas)
            if (
                str_contains($accionUpper, 'BORRADO') ||
                str_contains($accionUpper, 'ELIMINÓ') ||
                str_contains($accionUpper, 'FALLIDO')
            ) {
                $bordeColor = 'border-danger';
            }
        } else {
            // 👁️ VISTA ADMIN: Texto completo y radiografía de colores
            if (str_contains($accionUpper, 'CRÍTICO')) {
                $bordeColor = 'border-danger';
                $textoColor = 'text-danger fw-bolder'; // Rojo intenso
            } elseif (str_contains($accionUpper, 'LEVE')) {
                $bordeColor = 'border-warning';
                $textoColor = 'text-warning fw-bold'; // Naranja
            } elseif (
                str_contains($accionUpper, 'BORRADO') ||
                str_contains($accionUpper, 'ELIMINÓ') ||
                str_contains($accionUpper, 'FALLIDO')
            ) {
                $bordeColor = 'border-danger';
                $textoColor = 'text-danger fw-semibold';
            }
        }
    @endphp

    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                {{-- TARJETA DE DETALLES DEL LOG --}}
                <div class="card border-0 shadow-sm rounded-4 custom-card">
                    
                    {{-- Encabezado --}}
                    <div class="card-header bg-transparent border-bottom-0 pt-4 px-4 pb-2">
                        <h5 class="fw-bold mb-0 text-main" style="color: #1e293b;">
                            <i class="fas fa-file-signature me-2 text-primary"></i> Detalle del Registro de Auditoría #{{ $log->id }}
                        </h5>
                        <p class="text-muted small mt-1 mb-0">Información detallada sobre la actividad registrada en el sistema.</p>
                    </div>

                    <div class="card-body p-4">
                        
                        {{-- Información en Cuadrícula --}}
                        <div class="row g-4 mb-4">
                            
                            {{-- Operador --}}
                            <div class="col-md-6">
                                <label class="text-muted fw-bold text-uppercase x-small d-block mb-2" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                                    Usuario
                                </label>
                                <div class="d-flex align-items-center p-3 bg-dark rounded-3 border gap-3 h-100">
                                    @if($log->user)
                                        {{-- 1. Círculo del Avatar unificado (42px) --}}
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold shadow-sm"
                                            style="width: 42px; height: 42px; font-size: 1.1rem; min-width: 42px;">
                                            {{ strtoupper(substr($log->user->name ?? ($log->user->CardName ?? 'U'), 0, 1)) }}
                                        </div>

                                        {{-- 2. Información de Nombre y Rol --}}
                                        <div>
                                            <span class="d-block fw-bold mb-1" style="font-size: 0.95rem; color: inherit;">
                                                {{ $log->user->CardName ?? $log->user->name ?? 'Usuario del Sistema' }}
                                            </span>
                                            <span class="text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; color: #94a3b8;">
                                                {{ $log->user->role ?? $log->user->email ?? 'superadmin' }}
                                            </span>
                                        </div>
                                    @else
                                        {{-- Avatar para Sistema/Desconocido --}}
                                        <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center shadow-sm" 
                                             style="width: 42px; height: 42px; font-size: 1.1rem; min-width: 42px;">
                                            <i class="fas fa-user-slash"></i>
                                        </div>
                                        <div>
                                            <span class="d-block fw-bold mb-1" style="font-size: 0.95rem; color: inherit;">
                                                Sistema / Desconocido
                                            </span>
                                            <span class="text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; color: #94a3b8;">
                                                Automático
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Fecha y Hora --}}
                            <div class="col-md-6">
                                <label class="text-muted fw-bold text-uppercase x-small d-block mb-2" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                                    Fecha y Hora del Evento
                                </label>
                                <div class="p-3 bg-dark rounded-3 border d-flex flex-column justify-content-center h-100">
                                    <span class="fw-bold text-dark d-block mb-1">
                                        <i class="far fa-calendar-alt me-2 text-primary"></i> {{ $log->created_at->format('d / m / Y') }}
                                    </span>
                                    <span class="fw-bold text-secondary d-block" style="font-size: 0.85rem;">
                                        <i class="far fa-clock me-2"></i> {{ $log->created_at->format('h:i A') }}
                                    </span>
                                </div>
                            </div>

                            {{-- Módulo --}}
                            <div class="col-md-4">
                                <label class="text-muted fw-bold text-uppercase x-small d-block mb-2" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                                    Módulo Afectado
                                </label>
                                <span class="badge bg-dark text-dark border fw-bold px-3 py-2 shadow-sm" style="font-size: 0.85rem;">
                                    <i class="fas fa-cube me-1 text-primary"></i> {{ $log->modulo ?? 'N/A' }}
                                </span>
                            </div>

                            {{-- Descripción de la Actividad (CON ESTILO TIPO CONSOLA PERO FONDO CLARO PARA COLORES) --}}
                            <div class="col-md-12">
                                <label class="text-muted fw-bold text-uppercase x-small d-block mb-2" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                                    Descripción de la Actividad
                                </label>
                                <div class="p-4 bg-dark rounded-3 shadow-inner border-start border-4 {{ $bordeColor }}" 
                                     style="font-family: 'Courier New', Courier, monospace; overflow-x: auto;">
                                    <span class="{{ $textoColor }}" style="font-size: 0.95rem; line-height: 1.5;">
                                        {{ $textoMostrar ?? 'No hay detalles de la acción registrados.' }}
                                    </span>
                                </div>
                            </div>

                        </div>

                        {{-- BOTONES DE ACCIÓN --}}
                        <div class="d-flex justify-content-end gap-2 mt-4 pt-4 border-top">
                            <a href="{{ route('logs.index') }}" class="btn btn-light border fw-bold text-secondary px-4 rounded-pill">
                                <i class="fas fa-arrow-left me-1"></i> Regresar
                            </a>

                            @if (!$esProveedor)
                                <form action="{{ route('logs.destroy', $log->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-danger fw-bold px-4 rounded-pill shadow-sm"
                                        onclick="
                                            let form = this.closest('form');
                                            Swal.fire({
                                                title: '¿Estás seguro?',
                                                text: '¡No podrás revertir esto! El registro de auditoría se eliminará permanentemente.',
                                                icon: 'warning',
                                                showCancelButton: true,
                                                confirmButtonColor: '#d33',
                                                cancelButtonColor: '#6c757d',
                                                confirmButtonText: '<i class=\'fas fa-trash me-1\'></i> Sí, eliminar',
                                                cancelButtonText: 'Cancelar',
                                                reverseButtons: true
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    form.submit();
                                                }
                                            });
                                        ">
                                        <i class="fas fa-trash me-1"></i> Eliminar Log
                                    </button>
                                </form>
                            @endif
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <style>
        .shadow-inner {
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, .05);
        }
    </style>
@endsection