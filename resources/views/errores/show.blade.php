@extends('layouts.app')

@section('content')
    <div class="container-fluid px-4 py-5">

        {{-- Header del Módulo --}}
        <x-module-header title="Detalles del Error" subtitle="Información completa sobre la excepción registrada"
            icon="fas fa-exclamation-circle text-danger" />

        <div class="row justify-content-center mt-4">
            <div class="col-12 col-xl-10">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-dark">
                            <i class="fas fa-bug text-danger me-2"></i> Reporte de Error #{{ $error->id }}
                        </h5>
                        <a href="{{ route('errores.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Volver a Errores
                        </a>
                        {{-- Botón Eliminar con SweetAlert --}}
                        <form id="delete-form-{{ $error->id }}" action="{{ route('errores.destroy', $error->id) }}"
                            method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-sm btn-outline-danger shadow-sm" title="Eliminar Registro"
                                onclick="confirmarEliminacion({{ $error->id }})">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </div>

                    <div class="card-body p-4 p-md-5">

                        {{-- Información Básica --}}
                        <div class="row g-4 mb-5">
                            <div class="col-md-6">
                                <h6 class="text-muted fw-bold mb-2 text-uppercase"
                                    style="font-size: 0.8rem; letter-spacing: 0.5px;">Usuario Afectado</h6>
                                @if ($error->user)
                                    <div class="bg-light p-3 rounded border d-flex align-items-center">
                                        {{-- Ícono genérico en lugar del avatar --}}
                                        <div class="bg-white rounded-circle shadow-sm d-flex justify-content-center align-items-center me-3"
                                            style="width: 50px; height: 50px;">
                                            <i class="fas fa-user text-primary fs-4"></i>
                                        </div>

                                        <div>
                                            <span class="d-block fw-bold text-dark fs-5">{{ $error->user->name }}</span>
                                            <div class="mt-1">
                                                <span
                                                    class="badge bg-secondary me-2">{{ strtoupper($error->user->role ?? 'N/A') }}</span>
                                                {{-- Agregamos el email si existe, para aprovechar la variable mapeada --}}
                                                @if ($error->user->email)
                                                    <small class="text-muted"><i class="fas fa-envelope me-1"></i>
                                                        {{ $error->user->email }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-secondary mb-0">
                                        <i class="fas fa-user-slash me-2"></i> Usuario Eliminado o Desconocido
                                    </div>
                                @endif
                            </div>

                            <div class="col-md-6">
                                <h6 class="text-muted fw-bold mb-2 text-uppercase"
                                    style="font-size: 0.8rem; letter-spacing: 0.5px;">Contexto del Evento</h6>
                                <ul class="list-group list-group-flush rounded border">
                                    <li
                                        class="list-group-item d-flex justify-content-between align-items-center py-3 bg-light">
                                        <span class="text-muted"><i class="fas fa-layer-group me-2"></i> Módulo</span>
                                        <span class="fw-bold badge bg-danger">{{ $error->modulo ?? 'N/A' }}</span>
                                    </li>
                                    <li
                                        class="list-group-item d-flex justify-content-between align-items-center py-3 bg-light">
                                        <span class="text-muted"><i class="fas fa-calendar-alt me-2"></i> Fecha y
                                            Hora</span>
                                        <span class="fw-bold">{{ $error->created_at->format('d/m/Y H:i:s') }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        {{-- Mensaje de Error Completo --}}
                        <h6 class="text-muted fw-bold mb-3 text-uppercase"
                            style="font-size: 0.8rem; letter-spacing: 0.5px;">Descripción Completa de la Excepción</h6>
                        <div class="bg-dark text-light p-4 rounded-3 shadow-inner"
                            style="font-family: 'Courier New', Courier, monospace; overflow-x: auto: pre-wrap; border-left: 5px solid #dc3545;">
                            {{ $error->accion ?? 'No hay detalles de la acción registrados.' }}
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>

    <style>
        .shadow-inner {
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, .08);
        }
    </style>

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
                }
            });
        }
    </script>
@endsection
