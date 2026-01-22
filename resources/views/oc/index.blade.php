@extends('layouts.app')

@section('content')
    <style>
        /* Fondo de página para que resalte la tarjeta */
        body {
            background-color: #f8f9fa;
        }

        /* Estilos de marca y colores */
        .bg-blue-ragon {
            background-color: #1a237e;
            color: white;
        }

        .status-upload {
            background-color: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #c8e6c9;
        }

        .status-upload .dot {
            background-color: #4caf50;
        }

        /* SEPARACIÓN REAL: Aumentamos el padding vertical de las filas */
        .table>tbody>tr>td {
            padding: 1.8rem 1rem !important;
            vertical-align: middle;
            border-bottom: 1px solid #f0f0f0;
        }

        /* Sombra y bordes de la tarjeta principal */
        .card {
            border: none;
            box-shadow: 0 0.5rem 2rem rgba(0, 0, 0, 0.1) !important;
            border-radius: 15px;
        }

        .avatar-circle {
            width: 45px;
            height: 45px;
            background-color: #f0f2f5;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            color: #1a237e;
        }

        /* Efecto al pasar el mouse sobre la fila */
        .table-hover tbody tr:hover {
            background-color: #fcfdfe !important;
        }

        /* Estilo del buscador */
        .input-group-custom {
            border-radius: 50px;
            overflow: hidden;
            border: 1px solid #dee2e6;
        }
    </style>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-11">

                <div class="card overflow-hidden">
                    <div class="card-header bg-white border-bottom py-4 px-4">
                        <div class="row g-4 align-items-center">
                            <div class="col-md-7">
                                <h3 class="fw-bold mb-1 text-dark">
                                    <i class="fas fa-file-invoice-dollar me-2 text-primary"></i>
                                    Gestión de Órdenes de Compra
                                </h3>
                                <p class="text-muted mb-0">Visualiza y gestiona los archivos cargados al sistema</p>
                            </div>
                            <div class="col-md-5 text-md-end">
                                <span class="badge bg-blue-ragon px-3 py-2 rounded-pill shadow-sm">
                                    {{ $ordenes->count() }} Registros encontrados
                                </span>
                            </div>

                            <div class="header-actions mt-5">
                                <a href="{{ route('home') }}" class="btn-ragon-outline">
                                    <i class="fas fa-th-large me-2"></i> PANEL DE CONTROL
                                </a>
                            </div>
                        </div>

                        {{-- Buscador --}}
                        <div class="row mt-4">
                            <div class="col-12">
                                <form action="{{ route('oc.index') }}" method="GET">
                                    <div class="input-group input-group-lg shadow-sm rounded-pill overflow-hidden border">
                                        <span class="input-group-text bg-white border-0 ps-4">
                                            <i class="fas fa-search text-muted"></i>
                                        </span>
                                        <input type="text" name="search" class="form-control border-0 px-3"
                                            placeholder="Buscar por proveedor o nombre de archivo..."
                                            value="{{ $search }}">
                                        <button type="submit" class="btn btn-primary px-5 fw-bold">Buscar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show rounded-4 mb-4" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4 text-uppercase fs-7 fw-bold text-secondary py-3">Estado</th>
                                        <th class="text-uppercase fs-7 fw-bold text-secondary">Proveedor / Usuario</th>
                                        <th class="text-uppercase fs-7 fw-bold text-secondary">Archivo OC</th>
                                        <th class="text-uppercase fs-7 fw-bold text-secondary text-center">Fecha de Carga
                                        </th>
                                        <th class="text-end pe-4 text-uppercase fs-7 fw-bold text-secondary">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($ordenes as $oc)
                                        <tr>
                                            <td class="ps-4">
                                                <span
                                                    class="status-upload px-3 py-2 rounded-3 d-inline-flex align-items-center fw-bold"
                                                    style="font-size: 0.75rem;">
                                                    <span class="dot me-2"
                                                        style="width: 8px; height: 8px; border-radius: 50%;"></span>
                                                    RECIBIDO
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle me-3">
                                                        <i class="fas fa-user-tie"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-dark mb-0">{{ $oc->user->name }}</div>
                                                        <div class="text-muted small">{{ $oc->user->email }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-file-csv text-muted fs-4 me-3"></i>
                                                    <span class="text-primary fw-semibold">{{ $oc->nombre_original }}</span>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="text-dark fw-bold">{{ $oc->created_at->format('d/m/Y') }}</div>
                                                <div class="text-muted small">{{ $oc->created_at->format('H:i') }} hrs</div>
                                            </td>
                                            <td class="text-end pe-4">
                                                <div class="d-inline-flex gap-2">
                                                    {{-- Busca esta línea en tu archivo index.blade.php y cámbiala por esta --}}
                                                    <a href="{{ route('oc.download', $oc->id) }}"
                                                        class="btn btn-primary btn-sm px-4 rounded-pill shadow-sm fw-bold"
                                                        target="_blank"> {{-- El target="_blank" ayuda a ver si hay un error oculto --}}
                                                        <i class="fas fa-download me-2"></i>Descargar
                                                    </a>
                                                </div>
                                            </td>
                                            {{-- Reemplaza el botón del ojo por este --}}
                                            <td class="text-end pe-4">
                                                <div class="d-inline-flex gap-2">
                                                    <a href="{{ route('oc.preview', $oc->id) }}"
                                                        class="btn btn-outline-light btn-sm text-dark border shadow-sm"
                                                        title="Previsualizar Contenido">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5 text-muted">
                                                <i class="fas fa-folder-open fa-3x mb-3 opacity-25"></i>
                                                <p>No se encontraron registros</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if (method_exists($ordenes, 'links') && $ordenes->hasPages())
                        <div class="card-footer bg-white border-top py-4">
                            <div class="d-flex justify-content-center">
                                {{ $ordenes->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
