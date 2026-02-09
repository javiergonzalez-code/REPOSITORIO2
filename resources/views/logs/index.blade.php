@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-11">

                <div class="card border-0 shadow-sm rounded-4 mb-4 custom-card">
                    <div class="card-body p-4 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="header-icon-box shadow-sm me-3"
                                style="width: 55px; height: 55px; display: flex; align-items: center; justify-content: center; border-radius: 12px;">
                                <i class="fas fa-terminal fa-lg"></i>
                            </div>
                            <div>
                                <h2 class="audit-title mb-0">
                                    LOGS</h2>
                                <div class="audit-subtitle">
                                    <span class="pulse-indicator"></span> MONITOREO
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('home') }}" class="btn-ragon-outline">
                            <i class="fas fa-th-large me-2"></i> PANEL DE CONTROL
                        </a>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 mb-2 custom-card">
                    <div class="card-header bg-transparent border-0 pt-4 px-4">
                        <h6 class="text-uppercase fw-black mb-0 text-muted" style="font-size: 1rem; letter-spacing: 1px;">
                            <i class="fas fa-filter me-2"></i> Filtros de búsqueda
                        </h6>
                    </div>
                    <div class="card-body p-4 ">
                        <form action="{{ route('logs.index') }}" method="GET" class="row g-3 align-items-end">
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label-custom">Búsqueda</label>
                                <input type="text" name="search" class="form-input" placeholder="Palabra clave..."
                                    value="{{ request('search') }}">
                            </div>

                            <div class="col-lg-3 col-md-6">
                                <label class="form-label-custom">Usuario</label>
                                <input class="form-input" list="usuariosList" name="user" placeholder="Nombre..."
                                    value="{{ request('user') }}">
                                <datalist id="usuariosList">
                                    @foreach ($usuarios_filtro as $u)
                                        <option value="{{ $u->name }}">
                                    @endforeach
                                </datalist>
                            </div>

                            <div class="col-lg-3 col-md-6">
                                <label class="form-label-custom">Actividad</label>
                                <select name="accion" class="form-input">
                                    <option value="">Todas</option>
                                    <option value="sesión" {{ request('accion') == 'sesión' ? 'selected' : '' }}>LOGIN
                                    </option>
                                    <option value="SUBIDA" {{ request('accion') == 'SUBIDA' ? 'selected' : '' }}>SUBIDA
                                    </option>
                                    <option value="BORRADO" {{ request('accion') == 'BORRADO' ? 'selected' : '' }}>BORRADO
                                    </option>
                                </select>
                            </div>

                            <div class="col-lg-2 col-md-6">
                                <label class="form-label-custom text-uppercase x-small fw-bold">Fecha</label>
                                <input type="date" name="fecha" class="form-input" value="{{ request('fecha') }}">
                            </div>

                            <div class="col-lg-3 col-md-12">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-gradient rounded-pill">
                                        <i class="fas fa-filter me-1"></i> Filtrar
                                    </button>
                                    <a href="{{ route('oc.index') }}" class="btn btn-gradient rounded-pill">
                                        <i class="fas fa-sync-alt text-muted"></i>
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- RECUADRO 3: TABLA DE DATOS --}}
                <div class="card border-0 shadow-sm rounded-4 custom-card overflow-hidden">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr class="text-uppercase"
                                    style="font-size: 0.7rem; font-weight: 800;letter-spacing: 0.5px;">
                                    <th class="ps-4 py-3">Operador</th>
                                    <th class="text-center py-3">Actividad</th>
                                    <th class="py-3">Módulo</th>
                                    <th class="text-center py-3">Fecha y Hora</th>
                                    <th class="text-end pe-4 py-3">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    @php
                                        $badgeStyle = match (true) {
                                            str_contains($log->accion, 'LOGIN') => 'status-upload',
                                            str_contains($log->accion, 'BORRADO') => 'status-error',
                                            default => 'status-general',
                                        };
                                    @endphp
                                    <tr class="log-row">
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-pill bg-blue-ragon-gradient shadow-sm"
                                                    style="font-size: 0.8rem;">
                                                    {{ strtoupper(substr($log->user->name ?? '?', 0, 1)) }}
                                                </div>
                                                <div class="ms-3">
                                                    <div class="fw-bold text-main mb-0" style="font-size: 0.9rem;">
                                                        {{ $log->user->name ?? 'Sistema' }}</div>
                                                    <div class="x-small text-muted">{{ $log->user->role ?? 'N/A' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="status-indicator {{ $badgeStyle }} d-inline-flex">
                                                <span class="dot"></span> {{ $log->accion }}
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge-outline">
                                                <i class="fas fa-cube me-1"></i> {{ $log->modulo }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="fw-bold d-block text-main mb-0"
                                                style="font-size: 0.85rem;">{{ $log->created_at->format('d/m/Y') }}</span>
                                            <span
                                                class="x-small text-muted font-monospace">{{ $log->created_at->format('h:i A') }}</span>
                                        </td>
                                        <td class="text-end pe-4">
                                            <button
                                                class="btn btn-sm btn-light border rounded-3 text-primary shadow-sm px-3">
                                                <i class="fas fa-terminal me-2"></i> Ver
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted fw-bold">No hay registros
                                            disponibles</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-center">
                    {{ $logs->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>

            </div>
        </div>
    </div>
@endsection
