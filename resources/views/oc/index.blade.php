@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-11">
                {{-- ALERTAS DE ÉXITO O ERROR --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-3" role="alert">
                        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 rounded-3" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                {{-- FIN ALERTAS --}}

                {{-- RECUADRO 1: ENCABEZADO --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
                    <div class="card-body p-4 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="header-icon-box shadow-sm me-3"
                                style="background: #0f172a; color: #3b82f6; width: 55px; height: 55px; display: flex; align-items: center; justify-content: center; border-radius: 12px;">
                                <i class="fas fa-file-invoice fa-lg"></i>
                            </div>
                            <div>
                                <h2 class="audit-title mb-0" style="font-size: 1.5rem; font-weight: 800; color: #1e293b;">
                                    GESTIÓN DE ÓRDENES DE COMPRA</h2>
                                <div class="audit-subtitle" style="font-size: 0.75rem; color: #64748b; font-weight: 700;">
                                    <span class="pulse-indicator"></span> {{ $ordenes->total() }} DOCUMENTOS ENCONTRADOS
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('home') }}" class="btn-ragon-outline">
                            <i class="fas fa-th-large me-2"></i> PANEL DE CONTROL
                        </a>
                    </div>
                </div>

                {{-- RECUADRO 2: FILTROS --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
                    <div class="card-body p-4">
                        <form action="{{ route('oc.index') }}" method="GET" class="row g-3 align-items-end">
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label-custom text-uppercase x-small fw-bold">Búsqueda General</label>
                                <input type="text" name="search" class="form-input" placeholder="Proveedor o archivo..." value="{{ request('search') }}">
                            </div>
                            <div class="col-lg-2 col-md-6">
                                <label class="form-label-custom text-uppercase x-small fw-bold">Cargado por</label>
                                <input class="form-input" list="usuariosList" name="user" placeholder="Usuario..." value="{{ request('user') }}">
                                <datalist id="usuariosList">
                                    @foreach ($usuarios_filtro ?? [] as $u)
                                        <option value="{{ $u->name }}">
                                    @endforeach
                                </datalist>
                            </div>
                            <div class="col-lg-2 col-md-6">
                                <label class="form-label-custom text-uppercase x-small fw-bold">Extensión</label>
                                <select name="extension" class="form-input">
                                    <option value="">Todas</option>
                                    <option value="csv" {{ request('extension') == 'csv' ? 'selected' : '' }}>CSV</option>
                                    <option value="xml" {{ request('extension') == 'xml' ? 'selected' : '' }}>XML</option>
                                </select>
                            </div>
                            <div class="col-lg-2 col-md-6">
                                <label class="form-label-custom text-uppercase x-small fw-bold">Fecha</label>
                                <input type="date" name="fecha" class="form-input" value="{{ request('fecha') }}">
                            </div>
                            <div class="col-lg-3 col-md-12">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary rounded-pill w-100">Filtrar</button>
                                    <a href="{{ route('oc.index') }}" class="btn btn-outline-secondary rounded-pill"><i class="fas fa-sync-alt"></i></a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- RECUADRO 3: TABLA --}}
                <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                                <tr class="text-uppercase" style="font-size: 0.7rem; font-weight: 800; color: #64748b;">
                                    <th class="ps-4 py-3">Estado</th>
                                    <th class="py-3">Proveedor / Usuario</th>
                                    <th class="py-3">Archivo</th>
                                    <th class="text-center py-3">Fecha de Carga</th>
                                    <th class="text-end pe-4 py-3">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ordenes as $oc)
                                    <tr class="log-row">
                                        <td class="ps-4">
                                            <span class="status-indicator status-upload d-inline-flex">
                                                <span class="dot"></span> RECIBIDO
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-pill bg-blue-ragon-gradient shadow-sm">
                                                    {{ strtoupper(substr($oc->user->name ?? '?', 0, 1)) }}
                                                </div>
                                                <div class="ms-3">
                                                    <div class="fw-bold text-dark mb-0">{{ $oc->user->name }}</div>
                                                    <div class="x-small text-muted">ID: #{{ str_pad($oc->user->id, 4, '0', STR_PAD_LEFT) }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @php $ext = pathinfo($oc->nombre_original, PATHINFO_EXTENSION); @endphp
                                                <i class="fas {{ $ext == 'csv' ? 'fa-file-csv text-success' : 'fa-file-code text-info' }} fs-5 me-2"></i>
                                                <span class="text-primary fw-semibold small">{{ $oc->nombre_original }}</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="fw-bold d-block text-dark mb-0 small">{{ $oc->created_at->format('d/m/Y') }}</span>
                                            <span class="text-muted font-monospace small">{{ $oc->created_at->format('H:i') }} hrs</span>
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="d-flex justify-content-end gap-2">
                                                <a href="{{ route('oc.preview', $oc->id) }}" class="action-btn btn-view" title="Ver"><i class="fas fa-eye"></i></a>
                                                <a href="{{ route('archivos.download', $oc->id) }}" class="action-btn btn-download" title="Descargar"><i class="fas fa-cloud-download-alt"></i></a>
                                                <form action="{{ route('oc.destroy', $oc->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="action-btn btn-delete" title="Eliminar" onclick="return confirm('¿Seguro que deseas eliminar esta OC?');">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center py-5 text-muted">No hay registros</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="mt-4 d-flex justify-content-center">
                    {{ $ordenes->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>

    {{-- LOS ESTILOS SE PONEN UNA SOLA VEZ AL FINAL --}}
    <style>
        .action-btn {
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            transition: all 0.2s ease;
            border: none;
            text-decoration: none;
            font-size: 0.9rem;
        }
        .btn-view { background-color: #f1f5f9; color: #64748b; }
        .btn-view:hover { background-color: #e2e8f0; transform: translateY(-2px); }
        
        .btn-download { background-color: #ecfdf5; color: #10b981; }
        .btn-download:hover { background-color: #10b981; color: white; transform: translateY(-2px); }
        
        .btn-delete { background-color: #fff1f2; color: #f43f5e; }
        .btn-delete:hover { background-color: #f43f5e; color: white; transform: translateY(-2px); }

        .avatar-pill {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 0.8rem;
        }
        .bg-blue-ragon-gradient { background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%); }
    </style>
@endsection