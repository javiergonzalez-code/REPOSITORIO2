@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-5">
    
    {{-- Header del Módulo --}}
    <x-module-header 
        title="Detalles del Error" 
        subtitle="Información completa sobre la excepción registrada" 
        icon="fas fa-exclamation-circle text-danger" 
    />

    <div class="row justify-content-center mt-4">
        <div class="col-12 col-xl-10">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="fas fa-bug text-danger me-2"></i> Reporte de Error #{{ $error->id }}
                    </h5>
                    <a href="{{ route('errores.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Volver a Errores
                    </a>
                </div>
                
                <div class="card-body p-4 p-md-5">
                    
                    {{-- Información Básica --}}
                    <div class="row g-4 mb-5">
                        <div class="col-md-6">
                            <h6 class="text-muted fw-bold mb-2 text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Usuario Afectado</h6>
                            @if($error->user)
                                <div class="d-flex align-items-center bg-light p-3 rounded border">
                                    {{-- CORRECCIÓN 1: Inyección manual de propiedades SQL Server al Avatar --}}
                                    <x-user-avatar :name="$error->user->CardName" :userId="$error->user->CardCode" />
                                    <div class="ms-3">
                                        {{-- CORRECCIÓN 2: Llamada a CardName en lugar de name --}}
                                        <span class="d-block fw-bold text-dark fs-5">{{ $error->user->CardName }}</span>
                                        <span class="badge bg-secondary">{{ strtoupper($error->user->role ?? 'N/A') }}</span>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-secondary mb-0">Usuario Eliminado o Desconocido</div>
                            @endif
                        </div>

                        <div class="col-md-6">
                            <h6 class="text-muted fw-bold mb-2 text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Contexto del Evento</h6>
                            <ul class="list-group list-group-flush rounded border">
                                <li class="list-group-item d-flex justify-content-between align-items-center py-3 bg-light">
                                    <span class="text-muted"><i class="fas fa-layer-group me-2"></i> Módulo</span>
                                    <span class="fw-bold badge bg-danger">{{ $error->modulo ?? 'N/A' }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center py-3 bg-light">
                                    <span class="text-muted"><i class="fas fa-calendar-alt me-2"></i> Fecha y Hora</span>
                                    <span class="fw-bold">{{ $error->created_at->format('d/m/Y H:i:s') }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    {{-- Mensaje de Error Completo --}}
                    <h6 class="text-muted fw-bold mb-3 text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Descripción Completa de la Excepción</h6>
                    <div class="bg-dark text-light p-4 rounded-3 shadow-inner" style="font-family: 'Courier New', Courier, monospace; overflow-x: auto; white-space: pre-wrap; border-left: 5px solid #dc3545;">
                        {{ $error->accion ?? 'No hay detalles de la acción registrados.' }}
                    </div>

                </div>
                <div class="card-footer bg-light border-top-0 py-3 text-center">
                    <p class="text-muted mb-0 small">Este registro es de solo lectura para auditoría técnica.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .shadow-inner {
        box-shadow: inset 0 2px 4px rgba(0,0,0,.08);
    }
</style>
@endsection