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
            max-width: 1300px !important;
            /* El ancho ideal para que no se vea pegado a los bordes */
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
            <div class="audit-header-wrapper mb-5 border-radius-5">
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
            {{-- Barra de Filtros Avanzados --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <form action="{{ route('logs.index') }}" method="GET" class="row g-3 align-items-end">

                        {{-- Búsqueda General --}}
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label small fw-bold text-secondary text-uppercase"
                                style="letter-spacing: 1px;">Búsqueda General</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i
                                        class="fas fa-search text-muted"></i></span>
                                <input type="text" name="search" class="form-control bg-light border-0 shadow-none"
                                    placeholder="Palabra clave..." value="{{ request('search') }}">
                            </div>
                        </div>

                        {{-- Filtro por Usuario --}}
                        <div class="col-lg-2 col-md-6">
                            <label class="form-label small fw-bold text-secondary text-uppercase"
                                style="letter-spacing: 1px;">Usuario</label>
                            <input class="form-control bg-light border-0 shadow-none" list="usuariosList" name="user"
                                placeholder="Escribir nombre..." value="{{ request('user') }}">
                            <datalist id="usuariosList">
                                @foreach ($usuarios_filtro as $u)
                                    <option value="{{ $u->name }}">
                                @endforeach
                            </datalist>
                        </div>

                        {{-- Filtro por Acción --}}
                        <div class="col-lg-2 col-md-6">
                            <label class="form-label small fw-bold text-secondary text-uppercase"
                                style="letter-spacing: 1px;">Actividad</label>
                            <select name="accion" class="form-select bg-light border-0 shadow-none">
                                <option value="">Todas las acciones</option>
                                <option value="LOGIN" {{ request('accion') == 'LOGIN' ? 'selected' : '' }}>LOGIN</option>
                                <option value="LOGOUT" {{ request('accion') == 'LOGOUT' ? 'selected' : '' }}>LOGOUT</option>
                                <option value="SUBIDA" {{ request('accion') == 'SUBIDA' ? 'selected' : '' }}>SUBIDA DE
                                    ARCHIVO</option>
                                <option value="DESCARGA" {{ request('accion') == 'DESCARGA' ? 'selected' : '' }}>DESCARGA
                                </option>
                                <option value="BORRADO" {{ request('accion') == 'BORRADO' ? 'selected' : '' }}>BORRADO
                                </option>
                            </select>
                        </div>

                        {{-- Filtro por Fecha --}}
                        <div class="col-lg-2 col-md-6">
                            <label class="form-label small fw-bold text-secondary text-uppercase"
                                style="letter-spacing: 1px;">Fecha</label>
                            <input type="date" name="fecha" class="form-control bg-light border-0 shadow-none"
                                value="{{ request('fecha') }}">
                        </div>

                        {{-- Botones de Acción --}}
                        <div class="col-lg-3 col-md-12">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary w-100 rounded-3 fw-bold py-2 shadow-sm">
                                    <i class="fas fa-filter me-2"></i>FILTRAR
                                </button>
                                <a href="{{ route('logs.index') }}" class="btn btn-outline-secondary rounded-3 py-2 px-3"
                                    title="Limpiar Filtros">
                                    <i class="fas fa-sync-alt"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Tabla de Logs con Colores Inteligentes --}}
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-dark text-white">
                                <tr>
                                    <th class="ps-4 border-0">OPERADOR</th>
                                    <th class="border-0 text-center">ACTIVIDAD</th>
                                    <th class="border-0">MÓDULO</th>
                                    <th class="border-0 text-center">FECHA Y HORA</th>
                                    <th class="text-end pe-4 border-0">DETALLES</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white">
                                @forelse($logs as $log)
                                    @php
                                        // Lógica de colores según la acción
                                        $accion = strtoupper($log->accion);
                                        $badgeClass = 'bg-secondary'; // Color por defecto (Gris)

                                        if (str_contains($accion, 'LOGIN')) {
                                            $badgeClass = 'bg-success';
                                        }
                                        // Verde
                                        elseif (str_contains($accion, 'DESCARGA')) {
                                            $badgeClass = 'bg-info';
                                        }
                                        // Azul claro
                                        elseif (str_contains($accion, 'SUBIDA')) {
                                            $badgeClass = 'bg-primary';
                                        }
                                        // Azul fuerte
                                        elseif (str_contains($accion, 'BORRADO') || str_contains($accion, 'ERROR')) {
                                            $badgeClass = 'bg-danger';
                                        }
                                        // Rojo
                                        elseif (str_contains($accion, 'LOGOUT')) {
                                            $badgeClass = 'bg-warning text-dark';
                                        } // Amarillo
                                    @endphp
                                    <tr class="log-row">
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-pill bg-light text-primary fw-bold shadow-sm d-flex align-items-center justify-content-center rounded-circle"
                                                    style="width: 40px; height: 40px;">
                                                    {{ strtoupper(substr($log->user->name ?? '?', 0, 1)) }}
                                                </div>
                                                <div class="ms-3">
                                                    <div class="fw-bold text-dark mb-0">
                                                        {{ $log->user->name ?? 'Usuario Eliminado' }}</div>
                                                    <div class="text-muted small">Rol: {{ $log->user->role ?? 'N/A' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge {{ $badgeClass }} rounded-pill px-3 py-2 text-uppercase"
                                                style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                                {{ $log->accion }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-secondary small fw-bold"><i
                                                    class="fas fa-layer-group me-1"></i> {{ $log->modulo }}</span>
                                        </td>
                                        <td class="text-center font-monospace small">
                                            <span
                                                class="d-block text-dark fw-bold">{{ $log->created_at->format('d/m/Y') }}</span>
                                            <span class="text-muted">{{ $log->created_at->format('h:i A') }}</span>
                                        </td>
                                        <td class="text-end pe-4">
                                            <button class="btn btn-sm btn-light border rounded-3 text-primary"
                                                title="Ver detalles técnicos">
                                                <i class="fas fa-terminal"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <img src="https://cdn-icons-png.flaticon.com/512/6134/6134065.png"
                                                alt="No logs" style="width: 80px; opacity: 0.5;">
                                            <p class="mt-3 text-muted fw-bold">No se encontraron registros con esos
                                                filtros.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Paginación centrada --}}
            <div class="mt-4 d-flex justify-content-center">
                {{ $logs->links('pagination::bootstrap-5') }}
            </div>
