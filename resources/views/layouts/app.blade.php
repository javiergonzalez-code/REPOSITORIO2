<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'REPOSITORIO') }}</title>

    {{-- Favicon Corregido --}}
    <link rel="icon" href="{{ asset('img/favicon_grupo_ragon.ico') }}">

    {{-- FontAwesome y Bootstrap CSS --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Carga de CSS y JS con Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles {{-- Indispensable para tus componentes Volt/Livewire --}}
</head>

<body>
    <div id="app">
        {{-- Componente de Navegación --}}
        <x-navbar />

        <main class="py-4 container">
            @yield('content')
        </main>
    </div>

    {{-- SCRIPTS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @livewireScripts {{-- Indispensable para que funcionen los filtros y buscadores --}}

    {{-- Componente de alertas --}}
    <x-global-alerts />

    @stack('scripts')
</body>
</html>