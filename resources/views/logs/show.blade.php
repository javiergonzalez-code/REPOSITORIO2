@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                {{-- TARJETA DE DETALLES DEL LOG --}}
                <div class="card border-0 shadow-sm rounded-4 custom-card">

                    {{-- Encabezado --}}
                    <div class="card-header bg-transparent border-bottom-0 pt-4 px-4 pb-2">
                        <h5 class="fw-bold mb-0 text-main" style="color: #1e293b;">
                            <i class="fas fa-file-signature me-2 text-primary"></i> Detalle del Registro de Auditoría
                        </h5>
                        <p class="text-muted small mt-1 mb-0">Información detallada sobre la actividad registrada en el
                            sistema.</p>
                    </div>

                    <div class="card-body p-4">

                        {{-- Información en Cuadrícula --}}
                        <div class="row g-4 mb-4">

                            {{-- Operador --}}
                            <div class="col-md-6">
                                <label class="text-muted fw-bold text-uppercase x-small d-block mb-2"
                                    style="font-size: 0.7rem; letter-spacing: 0.5px;">
                                    Usuario
                                </label>
                                {{-- Cambiamos p-2 por p-3, agregamos h-100 y gap-3 para el espacio --}}
                                <div class="d-flex align-items-center p-3 bg-light rounded-3 border gap-3 h-100">
                                    {{-- 1. Círculo del Avatar --}}
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold shadow-sm"
                                        style="width: 42px; height: 42px; font-size: 1.1rem; min-width: 42px;">
                                        {{ strtoupper(substr($log->user->name ?? ($log->user->CardName ?? 'U'), 0, 1)) }}
                                    </div>

                                    {{-- 2. Información de Nombre y Rol --}}
                                    <div>
                                        <span class="d-block fw-bold mb-1" style="font-size: 0.95rem; color: inherit;">
                                            {{ $log->user->CardName ?? 'Usuario del Sistema' }}
                                        </span>
                                        <span class="text-uppercase"
                                            style="font-size: 0.75rem; letter-spacing: 0.5px; color: #94a3b8;">
                                            {{ $log->user->role ?? 'superadmin' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Fecha y Hora --}}
                            <div class="col-md-6">
                                <label class="text-muted fw-bold text-uppercase x-small d-block mb-2"
                                    style="font-size: 0.7rem; letter-spacing: 0.5px;">
                                    Fecha y Hora del Evento
                                </label>
                                <div class="p-3 bg-light rounded-3 border d-flex flex-column justify-content-center h-100">
                                    <span class="fw-bold text-dark d-block mb-1">
                                        <i class="far fa-calendar-alt me-2 text-primary"></i>
                                        {{ $log->created_at->format('d / m / Y') }}
                                    </span>
                                    <span class="fw-bold text-secondary d-block" style="font-size: 0.85rem;">
                                        <i class="far fa-clock me-2"></i> {{ $log->created_at->format('h:i A') }}
                                    </span>
                                </div>
                            </div>

                            {{-- Módulo --}}
                            <div class="col-md-4">
                                <label class="text-muted fw-bold text-uppercase x-small d-block mb-2"
                                    style="font-size: 0.7rem; letter-spacing: 0.5px;">
                                    Módulo Afectado
                                </label>
                                <span class="badge bg-white text-dark border fw-bold px-3 py-2 shadow-sm"
                                    style="font-size: 0.85rem;">
                                    <i class="fas fa-cube me-1 text-primary"></i> {{ $log->modulo ?? 'N/A' }}
                                </span>
                            </div>

                            {{-- Acción --}}
                            <div class="col-md-12">
                                <label class="text-muted fw-bold text-uppercase x-small d-block mb-2"
                                    style="font-size: 0.7rem; letter-spacing: 0.5px;">
                                    Descripción de la Actividad
                                </label>
                                <div class="p-4 bg-light rounded-3 border-start border-4 border-primary shadow-sm">
                                    <span class="fw-semibold text-dark" style="font-size: 0.95rem; line-height: 1.5;">
                                        {{ $log->accion }}
                                    </span>
                                </div>
                            </div>

                        </div>

                        {{-- BOTONES DE ACCIÓN --}}
                        <div class="d-flex justify-content-end gap-2 mt-4 pt-4 border-top">
                            <a href="{{ route('logs.index') }}"
                                class="btn btn-light border fw-bold text-secondary px-4 rounded-pill">
                                <i class="fas fa-arrow-left me-1"></i> Regresar
                            </a>

                            {{-- Formulario para eliminar interceptado con SweetAlert --}}
                            <form action="{{ route('logs.destroy', $log->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')

                                {{-- Cambiamos type="submit" por type="button" para controlar el envío --}}
                                <button type="button" class="btn btn-danger fw-bold px-4 rounded-pill shadow-sm"
                                    onclick="
                                    let form = this.closest('form');
                                    Swal.fire({
                                        title: '¿Estás seguro?',
                                        text: '¿Deseas eliminar este registro de auditoría permanentemente?',
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
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
