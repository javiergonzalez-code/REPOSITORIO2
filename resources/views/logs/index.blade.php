@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-11">
                
                {{-- RECUADRO 1: TÍTULO (Usando nuestro componente reutilizable) --}}
                <x-module-header 
                    icon="fas fa-file-medical-alt" 
                    title="REGISTRO DE ACTIVIDAD (LOGS)" 
                    subtitle="MÓDULO DE AUDITORÍA"
                />

                {{-- RECUADRO 2: EL COMPONENTE LIVEWIRE --}}
                <livewire:logs-manager />

            </div>
        </div>
    </div>
@endsection