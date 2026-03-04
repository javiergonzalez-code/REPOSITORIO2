@extends('layouts.app')

@section('content')
    <div class="login-wrapper">
        <div class="card login-card card-large shadow-lg">
            
            {{-- Cabecera visual --}}
            <div class="card-header login-header-modern ">
                <div class="brand-icon-wrapper ">
                    <i class="fas fa-file-import"></i>
                </div>
                <h2 class="h3 fw-bold mb-0 mt-2">CARGA DE DATOS</h2>
                <span class="badge bg-blue-ragon mt-2 px-3">SISTEMA DE GESTIÓN RAGON</span>

                <div class="header-actions mt-4">
                    <a href="{{ route('home') }}" class="btn-ragon-outline">
                        <i class="fas fa-th-large me-2"></i> PANEL DE CONTROL
                    </a>
                </div>
            </div>

            {{-- Píldora de formatos --}}
            <div class="w-162 mt-4 text-center ">
                <div class="format-notice-pill">
                    <div class="d-flex align-items-center justify-content-center">
                        <span class="pulse-icon">
                            <i class="fas fa-info-circle"></i>
                        </span>
                        <p class="mb-0 mx-2">
                            Formatos permitidos:
                            <span class="badge-format">CSV</span>
                            <span class="badge-format">XML</span>
                            <span class="badge-format">XLSX</span>
                        </p>
                        <span class="size-limit">MÁX. 5MB</span>
                    </div>
                </div>
            </div>
            
            {{-- Cuerpo de la tarjeta --}}
            <div class="card-body">
                {{-- AQUÍ LLAMAMOS A LA MAGIA. Le pasamos la ruta donde guarda el controlador --}}
                <x-upload-zone action="{{ route('input.store') }}" />
            </div>
            
        </div>
    </div>
@endsection