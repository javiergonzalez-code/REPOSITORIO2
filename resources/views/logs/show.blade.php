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
                    <p class="text-muted small mt-1 mb-0">Información detallada sobre la actividad registrada en el sistema.</p>
                </div>

                <div class="card-body p-4">
                    
                    {{-- Información en Cuadrícula --}}
                    <div class="row g-4 mb-4">
                        
                        {{-- Operador --}}
                        <div class="col-md-6">
                            <label class="text-muted fw-bold text-uppercase x-small d-block mb-2" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                                Operador
                            </label>
                            <div class="d-flex align-items-center p-2 bg-light rounded-3 border">
                                @if($log->user)
                                    <x-user-avatar :user="$log->user" />
                                    <div class="ms-3">
                                        <span class="d-block fw-bold text-dark" style="font-size: 0.9rem;">
                                            {{ $log->user->CardName ?? $log->user->name ?? 'Usuario sin nombre' }}
                                        </span>
                                        <span class="d-block text-muted" style="font-size: 0.75rem;">
                                            {{ $log->user->E_Mail ?? 'Sin correo' }}
                                        </span>
                                    </div>
                                @else
                                    <div class="d-flex justify-content-center align-items-center bg-secondary text-white rounded-circle" style="width: 40px; height: 40px;">
                                        <i class="fas fa-robot"></i>
                                    </div>
                                    <span class="ms-3 fw-bold text-dark">Sistema / Desconocido</span>
                                @endif
                            </div>
                        </div>

                        {{-- Fecha y Hora --}}
                        <div class="col-md-6">
                            <label class="text-muted fw-bold text-uppercase x-small d-block mb-2" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                                Fecha y Hora del Evento
                            </label>
                            <div class="p-3 bg-light rounded-3 border d-flex flex-column justify-content-center h-100">
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
                            <span class="badge bg-white text-dark border fw-bold px-3 py-2 shadow-sm" style="font-size: 0.85rem;">
                                <i class="fas fa-cube me-1 text-primary"></i> {{ $log->modulo ?? 'N/A' }}
                            </span>
                        </div>

                        {{-- Acción (Ocupa todo el ancho por si es un texto largo) --}}
                        <div class="col-md-12">
                            <label class="text-muted fw-bold text-uppercase x-small d-block mb-2" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                                Descripción de la Actividad
                            </label>
                            <div class="p-4 bg-light rounded-3 border-start border-4 border-primary shadow-sm">
                                <span class="fw-semibold text-dark" style="font-size: 0.95rem; line-height: 1.5;">
                                    {{ $log->accion }}
                                </span>
                            </div>
                        </div>

                    </div>

                    {{-- AQUÍ ESTÁN TUS BOTONES --}}
                    <div class="d-flex justify-content-end gap-2 mt-4 pt-4 border-top">
                        <a href="{{ route('logs.index') }}" class="btn btn-light border fw-bold text-secondary px-4 rounded-pill">
                            <i class="fas fa-arrow-left me-1"></i> Regresar
                        </a>

                        {{-- Formulario para eliminar desde la vista Show --}}
                        <form action="{{ route('logs.destroy', $log->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger fw-bold px-4 rounded-pill shadow-sm" onclick="return confirm('¿Estás seguro de que deseas eliminar este registro de auditoría permanentemente?');">
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