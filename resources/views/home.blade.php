@extends('app')
@section('content')
<main class="login-form">
    <div class="cotainer">
        <div class="row justify-content-center">
            {{-- Encabezado de Bienvenida --}}
            <div class="text-center my-5">
                <h1 class="display-5 font-weight-bold">Panel de Control</h1>
                <p class="text-muted">Bienvenido de nuevo, {{-- Auth::user()->name --}}. Selecciona una sección para empezar a gestionar.</p>
            </div>
        </div>

            {{-- Contenedor de la Cuadrícula (Grid) --}}
            <div class="dashboard-grid">

                {{-- Tarjeta para Errores --}}
                <a href="{{ route('errores.index') }}" class="dashboard-card">
                    <i class="fas fa-user-graduate fa-3x mb-3"></i>
                    <h3 class="font-weight-bold">Errores</h3>    
                </a>

                {{-- Tarjeta para Errores --}}
                <a href="{{ route('input.index') }}" class="dashboard-card">
                    <i class="fas fa-user-graduate fa-3x mb-3"></i>
                    <h3 class="font-weight-bold">Input</h3>  
                </a>

                {{-- Tarjeta para Logs --}}
                <a href="{{ route('logs.index') }}" class="dashboard-card">
                    <i class="fas fa-user-graduate fa-3x mb-3"></i>
                    <h3 class="font-weight-bold">Errores</h3>    
                </a>

                {{-- Tarjeta para OC --}}
                <a href="{{ route('oc.index') }}" class="dashboard-card">
                    <i class="fas fa-user-graduate fa-3x mb-3"></i>
                    <h3 class="font-weight-bold">OC</h3>    
                </a>
            </div>

            
    </div>
</main>
@endsection
                                