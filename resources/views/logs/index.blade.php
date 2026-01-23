@extends('layouts.app')

@section('content')
<style>
    /* Forzamos el centrado total del contenedor principal */
    .main-centering-wrapper {
        display: flex !important;
        justify-content: center !important;
        width: 100% !important;
        margin: 0 auto !important;
        padding: 0 !important;
    }

    .custom-max-width {
        width: 100% !important;
        max-width: 1300px !important; /* El ancho ideal para que no se vea pegado a los bordes */
    }

    /* Esto quita cualquier margen izquierdo que Laravel/Bootstrap ponga por defecto */
    .container-fluid {
        margin-left: auto !important;
        margin-right: auto !important;
    }
</style>

<div class="main-centering-wrapper">
    <div class="custom-max-width py-5 px-3">
        
        {{-- Encabezado --}}
        <div class="audit-header-wrapper mb-5">
            <div class="d-flex justify-content-between align-items-center p-4 bg-white shadow-sm rounded-4 border-0">
                <div class="d-flex align-items-center">
                    <div class="header-icon-box me-4">
                        <i class="fas fa-shield-check"></i>
                    </div>
                    <div>
                        <h2 class="audit-title mb-0">LOGS</h2>
                        <div class="audit-subtitle">
                            <span class="pulse-indicator"></span>
                            SISTEMA DE MONITOREO EN TIEMPO REAL • RAGON
                        </div>
                    </div>
                </div>
                <div class="header-actions mt-5">
                    <a href="{{ route('home') }}" class="btn-ragon-outline">
                        <i class="fas fa-th-large me-2"></i> PANEL DE CONTROL
                    </a>
                </div>
            </div>
        </div>

        {{-- Tabla --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-dark text-white">
                            <tr>
                                <th class="ps-4 border-0">OPERADOR</th>
                                <th class="border-0">EVENTO / ACTIVIDAD</th>
                                <th class="border-0">MÓDULO</th>
                                <th class="border-0 text-center">TIMESTAMP</th>
                                <th class="border-0 text-center">DIRECCIÓN IP</th>
                                <th class="text-end pe-4 border-0">ACCIÓN</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            @forelse($logs as $log)
                            <tr class="log-row">
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-pill bg-blue-ragon-gradient shadow-sm">
                                            {{ strtoupper(substr($log->user->name, 0, 1)) }}
                                        </div>
                                        <div class="ms-3">
                                            <div class="fw-bold text-dark mb-0">{{ $log->user->name }}</div>
                                            <div class="text-muted x-small">ID: #00{{ $log->user->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="status-indicator {{ str_contains(strtolower($log->accion), 'error') ? 'status-error' : 'status-general' }}">
                                        <span class="dot"></span>
                                        <span class="text">{{ $log->accion }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge-outline">{{ $log->modulo }}</span>
                                </td>
                                <td class="text-center font-monospace small">
                                    <span class="d-block text-dark fw-bold">{{ $log->created_at->format('d M, Y') }}</span>
                                    <span class="text-muted">{{ $log->created_at->format('H:i:s') }}</span>
                                </td>
                                <td class="text-center">
                                    <code class="ip-address">{{ $log->ip ?? '0.0.0.0' }}</code>
                                </td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-action-icon"><i class="fas fa-terminal"></i></button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center py-5 text-muted">No hay registros.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-4 d-flex justify-content-center">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection