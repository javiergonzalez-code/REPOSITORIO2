@extends('layouts.app')

@section('content')
<div class="container">
    {{-- Encabezado de Bienvenida --}}
    <div class="text-center my-5">
        <h1 class="display-5 font-weight-bold">Panel de Control</h1>
        <p class="text-muted">Bienvenido de nuevo, {{ Auth::user()->name }}. Selecciona una sección para empezar a gestionar.</p>
    </div>

    {{-- Contenedor de la Cuadrícula (Grid) --}}
    <div class="dashboard-grid">

        {{-- Tarjeta para Alumnos --}}
        <a href="{{ route('alumnos.index') }}" class="dashboard-card">
            <i class="fas fa-user-graduate fa-3x mb-3"></i>
            <h3 class="font-weight-bold">Alumnos</h3>
            <p class="text-muted">Gestionar estudiantes y sus expedientes.</p>
        </a>

        {{-- Tarjeta para Profesores --}}
        <a href="{{ route('profesores.index') }}" class="dashboard-card">
            <i class="fas fa-chalkboard-teacher fa-3x mb-3"></i>
            <h3 class="font-weight-bold">Profesores</h3>
            <p class="text-muted">Administrar el personal docente.</p>
        </a>
        
        {{-- Tarjeta para Materias --}}
        <a href="{{ route('materias.index') }}" class="dashboard-card">
            <i class="fas fa-book fa-3x mb-3"></i>
            <h3 class="font-weight-bold">Materias</h3>
            <p class="text-muted">Crear y editar las asignaturas del plan de estudios.</p>
        </a>

        {{-- Tarjeta para Grupos --}}
        <a href="{{ route('grupos.index') }}" class="dashboard-card">
            <i class="fas fa-users fa-3x mb-3"></i>
            <h3 class="font-weight-bold">Grupos</h3>
            <p class="text-muted">Organizar a los alumnos en grupos y clases.</p>
        </a>
        
        {{-- Tarjeta para Carreras --}}
        <a href="{{ route('carreras.index') }}" class="dashboard-card">
            <i class="fas fa-graduation-cap fa-3x mb-3"></i>
            <h3 class="font-weight-bold">Carreras</h3>
            <p class="text-muted">Gestionar las carreras ofrecidas.</p>
        </a>

        {{-- Tarjeta para Áreas --}}
        <a href="{{ route('areas.index') }}" class="dashboard-card">
            <i class="fas fa-building fa-3x mb-3"></i>
            <h3 class="font-weight-bold">Áreas</h3>
            <p class="text-muted">Administrar las áreas académicas.</p>
        </a>
        
        {{-- Tarjeta para Paquetes --}}
        <a href="{{ route('paquetes.index') }}" class="dashboard-card">
            <i class="fas fa-box-open fa-3x mb-3"></i>
            <h3 class="font-weight-bold">Paquetes</h3>
            <p class="text-muted">Configurar paquetes de materias.</p>
        </a>

        {{-- Tarjeta para Historiales --}}
        <a href="{{ route('historiales.index') }}" class="dashboard-card">
            <i class="fas fa-history fa-3x mb-3"></i>
            <h3 class="font-weight-bold">Historiales</h3>
            <p class="text-muted">Consultar historiales académicos.</p>
        </a>
        
        {{-- Tarjeta para Materia-Profesores --}}
        <a href="{{ route('materia-profesores.index') }}" class="dashboard-card">
            <i class="fas fa-link fa-3x mb-3"></i>
            <h3 class="font-weight-bold">Asignaciones</h3>
            <p class="text-muted">Asignar materias a profesores.</p>
        </a>

        <!-- {{-- Tarjeta para Paquete-Materias --}}
        <a href="{{ route('paquete-materias.index') }}" class="dashboard-card">
            <i class="fas fa-sitemap fa-3x mb-3"></i>
            <h3 class="font-weight-bold">Contenido Paquetes</h3>
            <p class="text-muted">Definir las materias de cada paquete.</p>
        </a> -->
                <a href="{{ route('procedimientos.index') }}" class="dashboard-card">
            <i class="fas fa-cogs fa-3x mb-3"></i>
            <h3 class="font-weight-bold">Procedimientos</h3>
            <p class="text-muted">Ejecutar tareas administrativas.</p>
        </a>
    </div>
</div>
@endsection