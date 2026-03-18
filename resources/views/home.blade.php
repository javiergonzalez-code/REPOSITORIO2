@extends('layouts.app')

@section('content')
    <main class="login-form">
        <div class="container">
            <div class="row justify-content-center">
                {{-- Encabezado de Bienvenida --}}
                <div class="text-center mb-4">
                    <h1 class="display-5 fw-bold">Panel de Control</h1>
                    <p class="text-muted lead">
                        Bienvenido de nuevo, <span class="fw-bold text-primary">{{ Auth::user()->name }}</span>.
                        Selecciona una sección para empezar a gestionar.
                    </p>
                </div>
            </div>

            {{-- PANEL CENTRAL DE MANTENIMIENTO --}}
            @if (auth()->user()->hasRole('Super Admin') ||
                    auth()->user()->hasRole('Administrador') ||
                    auth()->user()->email === 'admin@ragon.com')
                <div class="row justify-content-center mb-4">
                    <div class="col-md-8"> {{-- Se hizo más ancho (col-md-8) para que quepan bien --}}
                        <div class="card border-warning shadow-sm">
                            <div class="card-header bg-warning text-dark fw-bold">
                                <i class="fas fa-tools"></i> Panel Central de Mantenimiento (Admin)
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    {{-- Switch OC --}}
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check form-switch fs-6">
                                            <input class="form-check-input cursor-pointer" type="checkbox" id="switchOC"
                                                {{ $mantenimientoOC ? 'checked' : '' }}
                                                onchange="toggleMantenimiento('oc')">
                                            <label class="form-check-label fw-bold text-dark" for="switchOC">Módulo
                                                OC</label>
                                            <div class="small text-muted">Suspender Órdenes de Compra a Operadores</div>
                                        </div>
                                    </div>

                                    {{-- Switch Inputs --}}
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check form-switch fs-6">
                                            <input class="form-check-input cursor-pointer" type="checkbox" id="switchInputs"
                                                {{ $mantenimientoInputs ? 'checked' : '' }}
                                                onchange="toggleMantenimiento('inputs')">
                                            <label class="form-check-label fw-bold text-dark" for="switchInputs">Módulo
                                                Input</label>
                                            <div class="small text-muted">Bloquear la carga de archivos</div>
                                        </div>
                                    </div>

                                    {{-- Switch Usuarios --}}
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <div class="form-check form-switch fs-6">
                                            <input class="form-check-input cursor-pointer" type="checkbox" id="switchUsers"
                                                {{ $mantenimientoUsers ? 'checked' : '' }}
                                                onchange="toggleMantenimiento('users')">
                                            <label class="form-check-label fw-bold text-dark" for="switchUsers">Módulo
                                                Usuarios</label>
                                            <div class="small text-muted">Bloquear edición y creación de cuentas</div>
                                        </div>
                                    </div>

                                    {{-- Switch Logs --}}
                                    <div class="col-md-6">
                                        <div class="form-check form-switch fs-6">
                                            <input class="form-check-input cursor-pointer" type="checkbox" id="switchLogs"
                                                {{ $mantenimientoLogs ? 'checked' : '' }}
                                                onchange="toggleMantenimiento('logs')">
                                            <label class="form-check-label fw-bold text-dark" for="switchLogs">Módulo
                                                Logs</label>
                                            <div class="small text-muted">Bloquear vista de auditoría</div>
                                        </div>
                                    </div>

                                    {{-- Switch Errores --}}
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check form-switch fs-6">
                                            <input class="form-check-input cursor-pointer" type="checkbox"
                                                id="switchErrores" {{ $mantenimientoErrores ? 'checked' : '' }}
                                                onchange="toggleMantenimiento('errores')">
                                            <label class="form-check-label fw-bold text-dark" for="switchErrores">Módulo
                                                Errores</label>
                                            <div class="small text-muted">Bloquear vista del registro de errores</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- CONTADORES DE PAPELERA DE RECICLAJE --}}
            @if (auth()->user()->hasRole('Super Admin') || auth()->user()->email === 'admin@ragon.com')
                <div class="row justify-content-center mb-4">
                    <div class="col-md-8">
                        <div class="row">
                            {{-- Usuarios Eliminados --}}
                            <div class="col-md-6 mb-3">
                                <div class="card border-danger shadow-sm h-100">
                                    <div class="card-body d-flex align-items-center">
                                        <div class="flex-shrink-0 bg-danger-lt p-3 rounded">
                                            <i class="fas fa-user-slash fa-2x text-danger"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <div class="text-uppercase text-muted fw-bold small">Usuarios en Papelera</div>
                                            <div class="h2 mb-0 fw-bold">{{ \App\Models\User::onlyTrashed()->count() }}
                                            </div>
                                            {{-- <a href="{{ backpack_url('user') }}?trashed=1"
                                                class="small text-danger fw-bold">
                                                <i class="fas fa-external-link-alt"></i> Gestionar en Backpack
                                            </a> --}}
                                        </div>
                                    </div>
                                    </div>
                                </div>

                                {{-- Archivos Eliminados --}}
                                <div class="col-md-6 mb-3">
                                    <div class="card border-info shadow-sm h-100">
                                        <div class="card-body d-flex align-items-center">
                                            <div class="flex-shrink-0 bg-info-lt p-3 rounded">
                                                <i class="fas fa-file-excel fa-2x text-info"></i>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <div class="text-uppercase text-muted fw-bold small">Archivos en Papelera
                                                </div>
                                                <div class="h2 mb-0 fw-bold">
                                                    {{ \App\Models\Archivo::onlyTrashed()->count() }}
                                                </div>
                                                {{-- <a href="{{ backpack_url('archivo') }}?trashed=1"
                                                    class="small text-info fw-bold">
                                                    <i class="fas fa-external-link-alt"></i> Ver archivos borrados
                                                </a> --}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            @endif
            {{-- Contenedor de la Cuadrícula (Grid) --}}
            <div class="dashboard-grid">

                <x-dashboard-card route="{{ route('errores.index') }}" icon="fas fa-bug" title="Errores" />

                <x-dashboard-card route="{{ route('input.index') }}" icon="fas fa-file-upload" title="Input" />

                <x-dashboard-card route="{{ route('logs.index') }}" icon="fas fa-file-medical-alt" title="Logs" />

                <x-dashboard-card route="{{ route('oc.index') }}" icon="fas fa-file-invoice-dollar" title="OC" />

                {{-- Tarjeta para Manejo de usuarios --}}
                @if (auth()->user()->role === 'admin' ||
                        auth()->user()->role === 'superadmin' ||
                        auth()->user()->email === 'admin@ragon.com')
                    <x-dashboard-card route="{{ route('users.index') }}" icon="fas fa-user" title="Usuarios" />
                @endif

                {{-- Tarjeta para Superusuario (Migrada a Laravel Auth) --}}
                @if (auth()->user() && (auth()->user()->email === 'admin@ragon.com' || auth()->user()->hasRole('Super Admin')))
                    <x-dashboard-card route="{{ url('admin') }}" icon="fas fa-user-shield" title="Superusuario" />
                @endif

            </div>

            @role('Super Admin')
                <div class="mt-4 module-header">
                    <h3>Panel de Administración General</h3>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        @livewire('user-manager')
                    </div>

                    <div class="col-md-12 mt-4">
                        @livewire('logs-manager')
                    </div>
                </div>
            @endrole
        </div>
    </main>

    {{-- SCRIPT AJAX PARA GUARDAR EL CAMBIO SIN RECARGAR LA PÁGINA --}}
    <script>
        function toggleMantenimiento(modulo) {
            // Hacemos la petición a la ruta que actualiza el estado
            fetch(`/mantenimiento/toggle/${modulo}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Mostrar alerta de éxito (asumiendo que usas SweetAlert)
                        Swal.fire({
                            icon: 'success',
                            title: 'Actualizado',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire('Error', 'No se pudo actualizar el estado', 'error');
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    Swal.fire('Error', 'Hubo un problema de conexión', 'error');
                });
        }
    </script>
@endsection
