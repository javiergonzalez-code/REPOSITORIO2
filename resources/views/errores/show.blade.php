@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            {{-- TARJETA DE DETALLES DEL ERROR --}}
            <div class="card border-0 shadow-sm rounded-4 custom-card">
                
                {{-- Encabezado --}}
                <div class="card-header bg-transparent border-bottom-0 pt-4 px-4 pb-2">
                    <h5 class="fw-bold mb-0 text-main" style="color: #1e293b;">
                        <i class="fas fa-bug me-2 text-danger"></i> Reporte de Error #{{ $error->id }}
                    </h5>
                    <p class="text-muted small mt-1 mb-0">Información completa sobre la excepción registrada en el sistema.</p>
                </div>

                <div class="card-body p-4">
                    
                    {{-- Información en Cuadrícula --}}
                    <div class="row g-4 mb-4">
                        
                        {{-- Usuario Afectado --}}
                        <div class="col-md-6">
                            <label class="text-muted fw-bold text-uppercase x-small d-block mb-2" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                                Usuario
                            </label>
                            {{-- CORRECCIÓN: Se cambió bg-dark por bg-light para que el texto sea legible --}}
                            <div class="d-flex align-items-center p-3 bg-light rounded-3 border gap-3 h-100">
                                @if($error->user)
                                    {{-- 1. Círculo del Avatar --}}
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold shadow-sm"
                                        style="width: 42px; height: 42px; font-size: 1.1rem; min-width: 42px;">
                                        {{ strtoupper(substr($error->user->CardName ?? ($error->user->name ?? 'U'), 0, 1)) }}
                                    </div>

                                    {{-- 2. Información de Nombre y Rol/Email --}}
                                    <div>
                                        <span class="d-block fw-bold mb-1 text-dark" style="font-size: 0.95rem;">
                                            {{ $error->user->CardName ?? ($error->user->name ?? 'Usuario sin nombre') }}
                                        </span>
                                        <span class="text-uppercase text-muted" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                            {{ $error->user->role ?? ($error->user->E_Mail ?? 'Sin rol') }}
                                        </span>
                                    </div>
                                @else
                                    {{-- Avatar para Sistema/Desconocido --}}
                                    <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center shadow-sm" 
                                         style="width: 42px; height: 42px; font-size: 1.1rem; min-width: 42px;">
                                        <i class="fas fa-user-slash"></i>
                                    </div>
                                    <div>
                                        <span class="d-block fw-bold mb-1 text-dark" style="font-size: 0.95rem;">
                                            Sistema / Desconocido
                                        </span>
                                        <span class="text-uppercase text-muted" style="font-size: 0.75rem; letter-spacing: 0.5px;">
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
                            {{-- CORRECCIÓN: Se cambió bg-dark por bg-light para evitar el camuflaje de texto --}}
                            <div class="p-3 bg-light rounded-3 border d-flex flex-column justify-content-center h-100">
                                <span class="fw-bold text-dark d-block mb-1">
                                    <i class="far fa-calendar-alt me-2 text-danger"></i> {{ $error->created_at->format('d / m / Y') }}
                                </span>
                                <span class="fw-bold text-secondary d-block" style="font-size: 0.85rem;">
                                    <i class="far fa-clock me-2"></i> {{ $error->created_at->format('H:i:s') }}
                                </span>
                            </div>
                        </div>

                        {{-- Módulo --}}
                        <div class="col-md-12"> {{-- CORRECCIÓN: Se cambió a col-md-12 para que no quede huérfano a la izquierda --}}
                            <label class="text-muted fw-bold text-uppercase x-small d-block mb-2" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                                Módulo Afectado
                            </label>
                            {{-- CORRECCIÓN: Se limpió el choque de bg-dark con text-dark. Ahora usa colores de alerta sutiles --}}
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle fw-bold px-3 py-2 shadow-sm" style="font-size: 0.85rem;">
                                <i class="fas fa-cube me-1"></i> {{ $error->modulo ?? 'N/A' }}
                            </span>
                        </div>

                        {{-- Descripción del Error --}}
                        <div class="col-md-12">
                            <label class="text-muted fw-bold text-uppercase x-small d-block mb-2" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                                Descripción Completa de la Excepción
                            </label>
                            {{-- NOTA: Aquí el bg-dark SÍ sirve porque emula una consola de comandos (código ligero) --}}
                            <div class="p-4 bg-dark text-white rounded-3 shadow-inner" style="font-family: 'Courier New', Courier, monospace; overflow-x: auto; border-left: 5px solid #dc3545;">
                                {{ $error->accion ?? 'No hay detalles de la acción registrados.' }}
                            </div>
                        </div>

                    </div>

                    {{-- BOTONES DE ACCIÓN --}}
                    <div class="d-flex justify-content-end gap-2 mt-4 pt-4 border-top">
                        <a href="{{ route('errores.index') }}" class="btn btn-light border fw-bold text-secondary px-4 rounded-pill">
                            <i class="fas fa-arrow-left me-1"></i> Regresar
                        </a>

                        {{-- Formulario con SweetAlert 2 --}}
                        <form action="{{ route('errores.destroy', $error->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-danger fw-bold px-4 rounded-pill shadow-sm"
                                onclick="
                                    let form = this.closest('form');
                                    Swal.fire({
                                        title: '¿Estás seguro?',
                                        text: '¡No podrás revertir esto! El registro de error se eliminará permanentemente.',
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonColor: '#dc3545',
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
                                <i class="fas fa-trash me-1"></i> Eliminar Registro
                            </button>
                        </form>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<style>
    .shadow-inner {
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, .2);
    }
</style>
@endsection