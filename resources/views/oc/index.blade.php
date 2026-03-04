@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-11">
                
                {{-- RECUADRO 1: TÍTULO (Usando nuestro nuevo componente) --}}
                <x-module-header 
                    icon="fas fa-file-invoice" 
                    title="GESTIÓN DE ÓRDENES DE COMPRA" 
                />

                {{-- RECUADRO 2: EL COMPONENTE LIVEWIRE --}}
                <livewire:oc-manager />

            </div>
        </div>
    </div>
@endsection