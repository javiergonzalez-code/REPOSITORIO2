@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-11">
                
                {{-- LLAMADA A NUESTRO COMPONENTE DE ENCABEZADO --}}
                <x-module-header 
                    icon="fas fa-users" 
                    title="ADMINISTRACIÓN DE USUARIOS" 
                    subtitle="CONTROL DE ACCESOS Y ROLES"
                />

                {{-- EL COMPONENTE LIVEWIRE --}}
                <livewire:user-manager />

            </div>
        </div>
    </div>
@endsection