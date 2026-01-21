@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold mb-0 text-dark">
                <i class="fas fa-file-invoice-dollar me-2 text-primary"></i>Gestión de Órdenes de Compra
            </h4>
            <span class="badge bg-blue-ragon px-3 py-2">Total: {{ $ordenes->count() }}</span>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Estado</th>
                            <th>Proveedor / Usuario</th>
                            <th>Archivo OC (CSV)</th>
                            <th>Fecha de Carga</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ordenes as $oc)
                        <tr>
                            <td class="ps-4">
                                <span class="status-upload p-2 rounded-3 d-inline-flex align-items-center" style="font-size: 0.75rem; font-weight: 700;">
                                    <span class="dot me-2" style="width: 8px; height: 8px; border-radius: 50%;"></span>
                                    RECIBIDO
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm me-3 bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                        <i class="fas fa-user text-muted"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $oc->user->name }}</div>
                                        <div class="text-muted small">{{ $oc->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="text-primary fw-medium">
                                    <i class="fas fa-file-csv me-1"></i> {{ $oc->nombre_original }}
                                </span>
                            </td>
                            <td>{{ $oc->created_at->format('d/m/Y H:i') }}</td>
                            <td class="text-end pe-4">
                                {{-- Botón para ver el JSON técnico --}}
                                <button class="btn btn-light btn-sm rounded-pill me-2" title="Ver Metadata">
                                    <i class="fas fa-terminal"></i>
                                </button>
                                {{-- Botón para descargar el CSV --}}
                                <a href="{{ asset('storage/' . $oc->nombre_sistema) }}" class="btn btn-primary btn-sm rounded-pill shadow-sm" download>
                                    <i class="fas fa-download me-1"></i> Descargar
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection