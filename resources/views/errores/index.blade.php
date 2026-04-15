@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-11">

                {{-- RECUADRO 1: TÍTULO (Usando nuestro componente reutilizable) --}}
                <x-module-header icon="fas fa-bug" title="REGISTRO DE ERRORES" subtitle="MÓDULO DE MONITOREO DEL SISTEMA" />

                {{-- RECUADRO 2: TABLA DE ERRORES (DISEÑO PREMIUM) --}}
                <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden custom-card">

                    {{-- Encabezado de la tabla --}}
                    <div
                        class="card-header bg-transparent border-0 pt-4 px-4 pb-2 d-flex justify-content-between align-items-center">
                        <h6 class="text-uppercase fw-black mb-0 text-danger" style="font-size: 0.9rem; letter-spacing: 1px;">
                            <i class="fas fa-exclamation-triangle me-2"></i> Excepciones Detectadas
                        </h6>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 border-top-0">

                                <thead>
                                    <tr class="text-muted"
                                        style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                        <th class="ps-4 py-3 border-0 rounded-start">Nivel / Estado</th>
                                        <th class="py-3 border-0">Usuario Afectado</th>
                                        <th class="py-3 border-0">Detalle del Error</th>
                                        <th class="text-center py-3 pe-4 border-0">Fecha y Hora</th>
                                        <th class="text-center py-3 pe-4 border-0 rounded-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="border-top-0">
                                    @forelse($erroresCarga as $error)
                                        <tr style="transition: all 0.2s ease;">
                                            {{-- COLUMNA 1: Nivel de Error --}}
                                            <td class="ps-4 py-3">
                                                <div class="status-indicator status-error">
                                                    <span class="dot"></span> FALLO DEL SISTEMA
                                                </div>
                                            </td>

                                            {{-- COLUMNA 2: Usuario --}}
                                            <td class="py-3">
                                                @if ($error->user)
                                                    <div class="d-flex align-items-center">
                                                        {{-- CRÍTICO: Mandamos CardName y CardCode al avatar --}}
                                                        <x-user-avatar :name="$error->user->CardName" :userId="$error->user->CardCode" :subtitle="$error->user->role" />
                                                    </div>
                                                @else
                                                    <span class="text-muted fst-italic">Usuario Eliminado / Desconocido</span>
                                                @endif
                                            </td>

                                            {{-- COLUMNA 3: Detalle del Error (Resumido) --}}
                                            <td class="py-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-light rounded p-2 me-3">
                                                        <i class="fas fa-exclamation-triangle text-danger"></i>
                                                    </div>
                                                    <div>
                                                        {{-- Muestra solo un resumen para no saturar la tabla --}}
                                                        <span class="d-block text-dark fw-medium"
                                                            style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                            {{ $error->accion ?? 'Error no especificado' }}
                                                        </span>
                                                        <span
                                                            class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle mt-1">
                                                            Módulo: {{ $error->modulo ?? 'Global' }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </td>

                                            {{-- COLUMNA 4: Fecha y Hora --}}
                                            <td class="text-center py-3">
                                                <span
                                                    class="d-block fw-bold text-dark">{{ $error->created_at->format('d M, Y') }}</span>
                                                <span
                                                    class="text-muted small">{{ $error->created_at->format('H:i:s') }}</span>
                                            </td>

                                            {{-- COLUMNA 5: Acciones (Cerrado correctamente) --}}
                                            <td class="text-center py-3 pe-4">
                                                <a href="{{ route('errores.show', $error->id) }}"
                                                    class="btn btn-sm btn-outline-primary shadow-sm" title="Ver Detalles">
                                                    <i class="fas fa-eye"></i> Ver
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5 text-muted">
                                                <div class="d-flex flex-column align-items-center">
                                                    <i class="fas fa-check-circle fs-1 text-success opacity-50 mb-3"></i>
                                                    <h5 class="fw-bold text-dark">No hay errores reportados</h5>
                                                    <p class="mb-0">El sistema funciona correctamente.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Paginación --}}
                @if (isset($logs) && method_exists($logs, 'links'))
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $logs->links('pagination::bootstrap-5') }}
                    </div>
                @elseif(isset($erroresCarga) && method_exists($erroresCarga, 'links'))
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $erroresCarga->links('pagination::bootstrap-5') }}
                    </div>
                @endif

            </div>
        </div>
    </div>
@endsection