@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-11">

                {{-- RECUADRO 1: TÍTULO --}}
                <x-module-header icon="fas fa-bug" title="REGISTRO DE ERRORES" subtitle="MÓDULO DE MONITOREO DEL SISTEMA" />

                {{-- RECUADRO 2: TABLA DE ERRORES --}}
                <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden custom-card">

                    {{-- Encabezado de la tabla --}}
                    <div
                        class="card-header bg-transparent border-0 pt-4 px-4 pb-2 d-flex justify-content-between align-items-center">
                        <h6 class="text-uppercase fw-bold mb-0 text-danger" style="font-size: 0.9rem; letter-spacing: 1px;">
                            <i class="fas fa-exclamation-triangle me-2"></i> Excepciones Detectadas
                        </h6>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 border-top-0">

                                <thead>
                                    <tr class="text-muted text-uppercase"
                                        style="font-size: 0.85rem; letter-spacing: 0.5px;">
                                        <th class="ps-4 py-3 border-0 rounded-start">Nivel / Estado</th>
                                        <th class="py-3 border-0">Usuario Afectado</th>
                                        <th class="py-3 border-0">Detalle del Error</th>
                                        <th class="text-center py-3 border-0">Fecha y Hora</th>
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
                                                <div class="d-flex align-items-center gap-3">
                                                    @if ($error->user)
                                                        {{-- 1. Círculo del Avatar --}}
                                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold shadow-sm"
                                                            style="width: 38px; height: 38px; font-size: 1rem; min-width: 38px;">
                                                            {{ strtoupper(substr($error->user->name ?? ($error->user->CardName ?? 'U'), 0, 1)) }}
                                                        </div>

                                                        {{-- 2. Información de Nombre y Rol --}}
                                                        <div>
                                                            <span class="d-block fw-bold mb-0"
                                                                style="font-size: 0.9rem; color: inherit;">
                                                                {{ $error->user->CardName ?? ($error->user->name ?? 'Usuario del Sistema') }}
                                                            </span>
                                                            <span class="text-uppercase"
                                                                style="font-size: 0.7rem; letter-spacing: 0.5px; color: #94a3b8;">
                                                                {{ $error->user->role ?? 'superadmin' }}
                                                            </span>
                                                        </div>
                                                    @else
                                                        {{-- Usuario Desconocido / Eliminado --}}
                                                        <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center shadow-sm"
                                                            style="width: 38px; height: 38px; font-size: 1rem; min-width: 38px;">
                                                            <i class="fas fa-user-slash"></i>
                                                        </div>

                                                        <div>
                                                            <span class="d-block fw-bold mb-0 text-muted"
                                                                style="font-size: 0.9rem;">
                                                                Usuario Eliminado / Desconocido
                                                            </span>
                                                            <span class="text-uppercase text-muted"
                                                                style="font-size: 0.7rem; letter-spacing: 0.5px;">
                                                                SISTEMA
                                                            </span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>

                                            {{-- COLUMNA 3: Detalle del Error --}}
                                            <td class="py-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-light rounded p-2 me-3">
                                                        <i class="fas fa-exclamation-triangle text-danger"></i>
                                                    </div>
                                                    <div>
                                                        {{-- Se reemplazó el CSS manual por la clase nativa 'text-truncate' --}}
                                                        <span class="d-block text-dark fw-medium text-truncate"
                                                            style="max-width: 250px;">
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

                                            {{-- COLUMNA DE BOTONES: VER Y ELIMINAR --}}
                                            <td class="text-center py-3 pe-4">
                                                <div class="d-flex justify-content-center gap-2">
                                                    {{-- Botón Ver --}}
                                                    <a href="{{ route('errores.show', $error->id) }}"
                                                        class="btn btn-sm btn-outline-primary rounded-circle"
                                                        title="Ver Detalles">
                                                        <i class="fas fa-eye"></i>
                                                    </a>

                                                    {{-- Botón Eliminar Integrado con SweetAlert 2 --}}
                                                    <form action="{{ route('errores.destroy', $error->id) }}" method="POST"
                                                        class="m-0 p-0">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button"
                                                            onclick="
                    let form = this.closest('form');
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: '¡No podrás revertir esto! El registro de error se eliminará permanentemente.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: '<i class=\'fas fa-trash me-1\'></i> Sí, eliminar',
                        cancelButtonText: 'Cancelar',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                "
                                                            class="btn btn-sm btn-outline-danger rounded-circle"
                                                            title="Eliminar Registro">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
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

                {{-- Paginación (Lógica simplificada y limpia) --}}
                @if (isset($erroresCarga) && $erroresCarga->hasPages())
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $erroresCarga->links('pagination::bootstrap-5') }}
                    </div>
                @endif

            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmarEliminacion(id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "¡No podrás revertir esto! El registro se eliminará de forma permanente.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-trash-alt me-1"></i> Sí, eliminar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Si el usuario confirma, buscamos el formulario por su ID y lo enviamos
                    document.getElementById('delete-form-' + id).submit();
                    window.history.back();
                }
            });
        }
    </script>
@endsection
