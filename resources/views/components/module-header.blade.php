@props([
    'icon', 
    'title', 
    'subtitle' => 'MÓDULO INTERACTIVO', 
    'backRoute' => null
])

<div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
    <div class="card-body p-4 d-flex justify-content-between align-items-center">
        
        {{-- Lado izquierdo: Icono y Textos --}}
        <div class="d-flex align-items-center">
            <div class="header-icon-box shadow-sm me-3"
                style="background: #0f172a; color: #3b82f6; width: 55px; height: 55px; display: flex; align-items: center; justify-content: center; border-radius: 12px;">
                <i class="{{ $icon }} fa-lg"></i>
            </div>
            <div>
                <h2 class="audit-title mb-0" style="font-size: 1.5rem; font-weight: 800; color: #1e293b;">
                    {{ $title }}
                </h2>
                <div class="audit-subtitle" style="font-size: 0.75rem; color: #64748b; font-weight: 700;">
                    <span class="pulse-indicator"></span> {{ $subtitle }}
                </div>
            </div>
        </div>

        {{-- Lado derecho: Botón de Regresar --}}
        <a href="{{ $backRoute ?? route('home') }}" class="btn-ragon-outline">
            <i class="fas fa-th-large me-2"></i> PANEL DE CONTROL
        </a>
        
    </div>
</div>