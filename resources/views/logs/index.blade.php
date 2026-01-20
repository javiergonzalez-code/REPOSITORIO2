@extends('layouts.app')

@section('content')
<div class="container-fluid py-5 px-lg-5">
{{-- Encabezado Estilo Neumórfico/Cristal --}}
<div class="audit-header-wrapper mb-5">
    <div class="d-flex justify-content-between align-items-center p-4 bg-white shadow-sm rounded-4 border">
        <div class="d-flex align-items-center">
            <div class="header-icon-box me-4">
                <i class="fas fa-shield-check"></i>
            </div>
            <div>
                <h2 class="audit-title">LOGS</h2>
                <div class="audit-subtitle">
                    <span class="pulse-indicator"></span>
                    SISTEMA DE MONITOREO EN TIEMPO REAL • RAGON
                </div>
            </div>
        </div>
        <div class="header-actions">
            <a href="{{ route('home') }}" class="btn-ragon-outline">
                <i class="fas fa-th-large me-2"></i> PANEL DE CONTROL
            </a>
        </div>
    </div>
</div>
    {{-- Tarjeta Principal --}}
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
        <th class="border-0 text-center">DIRECCIÓN IP</th> {{-- Columna separada --}}
        <th class="text-end pe-4 border-0">ACCIÓN</th> {{-- Columna separada --}}
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
                                @if(str_contains(strtolower($log->accion), 'subió'))
                                    <div class="status-indicator status-upload">
                                        <span class="dot"></span>
                                        <span class="text">{{ $log->accion }}</span>
                                    </div>
                                @elseif(str_contains(strtolower($log->accion), 'error'))
                                    <div class="status-indicator status-error">
                                        <span class="dot"></span>
                                        <span class="text">{{ $log->accion }}</span>
                                    </div>
                                @else
                                    <div class="status-indicator status-general">
                                        <span class="dot"></span>
                                        <span class="text">{{ $log->accion }}</span>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <span class="badge-outline">{{ $log->modulo }}</span>
                            </td>
                            <td class="text-center font-monospace small">
                                <span class="d-block text-dark">{{ $log->created_at->format('d M, Y') }}</span>
                                <span class="text-muted">{{ $log->created_at->format('H:i:s') }}</span>
                            </td>
                            <td class="text-center">
                                <code class="ip-address">{{ $log->ip ?? '0.0.0.0' }}</code>
                            </td>
                            <td class="text-end pe-4">
                                <button class="btn btn-action-icon" title="Ver detalles JSON">
                                    <i class="fas fa-terminal"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="80" class="opacity-25 mb-3">
                                <p class="text-muted fw-light">No se registran eventos en la base de datos.</p>
                            </td>
                        </tr>
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
@endsection