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

            {{-- PANEL DE MANTENIMIENTO (Fuera del grid para que se vea como una alerta/panel superior) --}}
            @can('admin system') {{-- Asegúrate de que este permiso existe en tus Seeders, o cámbialo por @role('Super Admin') --}}
            <div class="row justify-content-center mb-4">
                <div class="col-md-6">
                    <div class="card border-warning shadow-sm">
                        <div class="card-header bg-warning text-dark fw-bold">
                            <i class="fas fa-tools"></i> Panel de Mantenimiento (Admin)
                        </div>
                        <div class="card-body">
                            <div class="form-check form-switch fs-5">
                                <input class="form-check-input cursor-pointer" type="checkbox" id="switchOC" 
                                       {{ $mantenimientoOC ?? false ? 'checked' : '' }} 
                                       onchange="toggleMantenimiento('oc')">
                                <label class="form-check-label" for="switchOC">Suspender Módulo OC a Operadores</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endcan

            {{-- Contenedor de la Cuadrícula (Grid) --}}
            <div class="dashboard-grid">

                <x-dashboard-card route="{{ route('errores.index') }}" icon="fas fa-bug" title="Errores" />
                
                <x-dashboard-card route="{{ route('input.index') }}" icon="fas fa-file-upload" title="Input" />
                
                <x-dashboard-card route="{{ route('logs.index') }}" icon="fas fa-file-medical-alt" title="Logs" />
                
                <x-dashboard-card route="{{ route('oc.index') }}" icon="fas fa-file-invoice-dollar" title="OC" />
                
                {{-- Tarjeta para Manejo de usuarios --}}
                @if (auth()->user()->role === 'admin' || auth()->user()->role === 'superadmin' || auth()->user()->email === 'admin@ragon.com')
                    <x-dashboard-card route="{{ route('users.index') }}" icon="fas fa-user" title="Usuarios" />
                @endif

                {{-- Tarjeta para Superusuario (Backpack) --}}
                @if (backpack_user() && (backpack_user()->email === 'admin@ragon.com' || backpack_user()->role === 'superadmin'))
                    <x-dashboard-card route="{{ url('admin') }}" icon="fas fa-user-shield" title="Superusuario" />
                @endif

            </div>
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
                if(data.success) {
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