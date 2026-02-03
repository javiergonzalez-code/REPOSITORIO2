@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-11">

                <div class="mb-5 text-center text-md-start">
                    <div class="row align-items-center g-4">
                        <div class="col-md-7">
                            <h2 class="fw-bold text-dark mb-1">Gestión de Órdenes</h2>
                            <p class="text-muted">Panel centralizado de documentos cargados</p>
                        </div>
                        <div class="col-md-5 text-md-end mt-5">
                            <span class="badge bg-blue-ragon-gradient px-4 py-2 rounded-pill shadow-sm">
                                {{ $ordenes->total() }} Registros
                            </span>
                        </div>

                        <div class="header-actions mt-5">
                            <a href="{{ route('home') }}" class="btn-ragon-outline">
                                <i class="fas fa-th-large me-2"></i> PANEL DE CONTROL
                            </a>
                        </div>
                    </div>

                    <div class="row justify-content-center mt-4">
                        <div class="col-12">
                            <form action="{{ route('oc.index') }}" method="GET">
                                <div class="d-flex align-items-center gap-3 mb-5">
                                    <div class="search-container flex-grow-1 mb-5">
                                        <i class="fas fa-search text-muted"></i>
                                        <input type="text" name="search" class="search-input"
                                            placeholder="Buscar por proveedor o nombre de archivo..."
                                            value="{{ $search }}">
                                    </div>
                                    <button type="submit" class="btn btn-gradient px-5 py-3 rounded-pill fw-bold">
                                        Buscar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                @if (session('error'))
                    <div class="alert alert-danger border-0 rounded-4 mb-4">
                        <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                    </div>
                @endif

                <div class="card">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 text-uppercase small fw-bold text-muted py-3">Estado</th>
                                    <th class="text-uppercase small fw-bold text-muted">Proveedor / Usuario</th>
                                    <th class="text-uppercase small fw-bold text-muted">Archivo OC</th>
                                    <th class="text-uppercase small fw-bold text-muted text-center">Fecha de Carga</th>
                                    <th class="text-end pe-4 text-uppercase small fw-bold text-muted">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ordenes as $oc)
                                    <tr>
                                        <td class="ps-4">
                                            <span class="status-pill text-uppercase">
                                                <i class="fas fa-check-circle me-1"></i> Recibido
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-pill bg-blue-ragon-gradient">
                                                    {{ strtoupper(substr($oc->user->name, 0, 1)) }}
                                                </div>
                                                <div class="ms-3">
                                                    <div class="fw-bold text-dark small mb-0">{{ $oc->user->name }}</div>
                                                    <div class="text-muted" style="font-size: 0.7rem;">ID:
                                                        #{{ str_pad($oc->user->id, 4, '0', STR_PAD_LEFT) }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @php $ext = pathinfo($oc->nombre_original, PATHINFO_EXTENSION); @endphp
                                                @if ($ext == 'csv')
                                                    <i class="fas fa-file-csv text-success fs-4 me-3"></i>
                                                @else
                                                    <i class="fas fa-file-code text-info fs-4 me-3"></i>
                                                @endif
                                                <span
                                                    class="text-primary fw-semibold small text-break">{{ $oc->nombre_original }}</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="text-dark fw-bold small">{{ $oc->created_at->format('d/m/Y') }}
                                            </div>
                                            <div class="text-muted small" style="font-size: 0.7rem;">
                                                {{ $oc->created_at->format('H:i') }} hrs</div>
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="btn-group rounded-pill overflow-hidden">
                                                <a href="{{ route('oc.preview', $oc->id) }}"
                                                    class="btn btn-white btn-sm px-3" title="Previsualizar">
                                                    <i class="fas fa-eye text-muted"></i>
                                                </a>
                                                <a href="{{ route('oc.download', $oc->id) }}"
                                                    class="btn btn-white btn-sm px-3" title="Descargar">
                                                    <i class="fas fa-download text-primary"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <i class="fas fa-folder-open fa-3x text-light mb-3"></i>
                                            <p class="text-muted">No se encontraron registros de órdenes.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if (method_exists($ordenes, 'links') && $ordenes->hasPages())
                        <div class="card-footer bg-white border-0 py-4">
                            {{ $ordenes->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
