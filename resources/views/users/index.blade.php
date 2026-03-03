@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-4 mb-4">
        <div>
            <h1 class="audit-title mb-0" style="font-size: 1.5rem; font-weight: 800;">Control de Accesos</h1>
            <p class="text-muted mb-2">Gestión de proveedores y perfiles del sistema</p>
            <div class="audit-subtitle">
                <span class="pulse-indicator"></span> MÓDULO INTERACTIVO
            </div>
        </div>
        <div class="d-flex gap-3 align-items-center">
            <a href="{{ route('home') }}" class="btn-ragon-outline">
                <i class="fas fa-th-large me-2"></i> PANEL DE CONTROL
            </a>
            <a href="{{ route('users.create') }}" class="btn btn-gradient rounded-pill">
                <i class="fas fa-user-plus me-2"></i> NUEVO USUARIO
            </a>
        </div>
    </div>

    {{-- Llamamos a toda la magia de Volt --}}
    <livewire:user-manager />

</div>
@endsection