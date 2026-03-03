@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-11">

                {{-- RECUADRO 1: TÍTULO --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4 custom-card">
                    <div class="card-body p-4 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="header-icon-box shadow-sm me-3"
                                style="width: 55px; height: 55px; display: flex; align-items: center; justify-content: center; border-radius: 12px; background: #0f172a; color: #3b82f6;">
                                <i class="fas fa-terminal fa-lg"></i>
                            </div>
                            <div>
                                <h2 class="audit-title mb-0" style="font-size: 1.5rem; font-weight: 800;">
                                    LOGS DEL SISTEMA</h2>
                                <div class="audit-subtitle" style="font-size: 0.75rem; color: #64748b; font-weight: 700;">
                                    <span class="pulse-indicator"></span> MONITOREO INTERACTIVO EN TIEMPO REAL
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('home') }}" class="btn-ragon-outline">
                            <i class="fas fa-th-large me-2"></i> PANEL DE CONTROL
                        </a>
                    </div>
                </div>

                {{-- Llamamos a toda la magia de Volt --}}
                <livewire:logs-manager />

            </div>
        </div>
    </div>
@endsection