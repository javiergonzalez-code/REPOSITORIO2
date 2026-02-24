@extends('layouts.app')

@section('content')
    <main class="login-form">
        <div class="container">
            <div class="row justify-content-center">
                {{-- Encabezado de Bienvenida --}}
                <div class="text-center ">
                    <h1 class="display-5 fw-bold">Panel de Control</h1>
                    <p class="text-muted lead">
                        Bienvenido de nuevo, <span class="fw-bold text-primary">{{ Auth::user()->name }}</span>.
                        Selecciona una sección para empezar a gestionar.
                    </p>
                </div>
            </div>

            {{-- Contenedor de la Cuadrícula (Grid) --}}
            <div class="dashboard-grid">

                {{-- Tarjeta para Errores --}}
                <a href="{{ route('errores.index') }}" class="dashboard-card">
                    <i class="fas fa-bug fa-2x"></i>
                    <h3 class="fw-bold">Errores</h3>
                </a>

                {{-- Tarjeta para Input --}}
                <a href="{{ route('input.index') }}" class="dashboard-card">
                    <i class="fas fa-file-upload fa-2x"></i>
                    <h3 class="fw-bold">Input</h3>
                </a>

                {{-- Tarjeta para Logs --}}
                <a href="{{ route('logs.index') }}" class="dashboard-card">
                    <i class="fas fa-file-medical-alt fa-2x"></i>
                    <h3 class="fw-bold">Logs</h3>
                </a>

                {{-- Tarjeta para OC --}}
                <a href="{{ route('oc.index') }}" class="dashboard-card">
                    <i class="fas fa-file-invoice-dollar fa-2x"></i>
                    <h3 class="fw-bold">OC</h3>
                </a>

                {{-- Tarjeta para Manejo de usuarios (Visible para admin y superadmin) --}}
                @if (auth()->user()->role === 'admin' || auth()->user()->role === 'superadmin' || auth()->user()->email === 'admin@ragon.com')
                    <a href="{{ route('users.index') }}" class="dashboard-card">
                        <i class="fas fa-user fa-2x"></i>
                        <h3 class="fw-bold">Usuarios</h3>
                    </a>
                @endif

                {{-- Tarjeta para Superusuario (Backpack Admin - Visible SOLO para el correo específico o superadmin) --}}
                @if (backpack_user() && (backpack_user()->email === 'admin@ragon.com' || backpack_user()->role === 'superadmin'))
                    <a href="{{ url('admin') }}" class="dashboard-card">
                        <i class="fas fa-user-shield fa-2x"></i>
                        <h3 class="fw-bold">Superusuario</h3>
                    </a>
                @endif

            </div>
        </div>
    </main>
@endsection
