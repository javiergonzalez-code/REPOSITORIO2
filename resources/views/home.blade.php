@extends('layouts.app')

@section('content')
    <main class="login-form">
        <div class="container">
            <div class="row justify-content-center">
                {{-- Encabezado de Bienvenida --}}
                <div class="text-center mb-4">
                    <h1 class="display-5 fw-bold">Panel de Control</h1>
                    <p class="text-muted lead">
                        {{-- CRÍTICO: Cambiamos ->name por ->CardName para SQL Server --}}
                        Bienvenido de nuevo, <span class="fw-bold text-primary">{{ Auth::user()->CardName }}</span>.
                        Selecciona una sección para empezar a gestionar.
                    </p>
                </div>
            </div>

            

            {{-- Contenedor de la Cuadrícula (Grid) --}}
            <div class="dashboard-grid">

                <x-dashboard-card route="{{ route('errores.index') }}" icon="fas fa-bug" title="Errores" />

                <x-dashboard-card route="{{ route('input.index') }}" icon="fas fa-file-upload" title="Input" />

                <x-dashboard-card route="{{ route('logs.index') }}" icon="fas fa-file-medical-alt" title="Logs" />

                <x-dashboard-card route="{{ route('oc.index') }}" icon="fas fa-file-invoice-dollar" title="OC" />

                {{-- Tarjeta para Manejo de usuarios --}}
                @if (in_array(Auth::user()->role, ['superadmin', 'admin']))
                    <x-dashboard-card route="{{ route('users.index') }}" icon="fas fa-user" title="Usuarios" />
                @endif

            </div>


        </div>
    </main>

@endsection
