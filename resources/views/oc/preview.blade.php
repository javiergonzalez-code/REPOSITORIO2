@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-11">

                {{-- RECUADRO 1: ENCABEZADO DE PREVISUALIZACIÓN --}}
                <x-preview-header :oc="$oc" :extension="$extension" />

                {{-- RECUADRO 2: CONTENIDO DE LA TABLA --}}
                <x-data-preview-table :data="$data" />

            </div>
        </div>
    </div>

    {{-- LLAMADA AL SCRIPT GLOBAL DE ELIMINACIÓN (El mismo que usamos en Livewire) --}}
    <x-delete-confirm-script />

@endsection