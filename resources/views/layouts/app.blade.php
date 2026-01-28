<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'REPOSITORIO') }}</title>

    {{-- FontAwesome para los iconos --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    {{-- Carga de CSS y JS con Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div id="app">
        {{-- Navbar con clases de Bootstrap y tu clase personalizada --}}
        <nav class="navbar navbar-expand-md navbar-dark navbar-custom shadow-lg">
    <div class="container-fluid px-4"> {{-- Usamos fluid y padding para dar aire a los lados --}}
        <a class="navbar-brand" href="{{ url('/home') }}">
            REPOSITORIO
        </a>

        {{-- Botón de Modo Oscuro --}}
            <button id="dark-mode-toggle" class="btn btn-link text-white me-3" style="text-decoration: none;">
                <i class="fas fa-moon" id="dark-mode-icon"></i>
            </button>

        <div class="ms-auto d-flex align-items-center"> {{-- Contenedor derecho directo --}}
            @auth
                <div class="dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center text-white" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle fa-lg me-2"></i>
                        <span class="d-none d-sm-inline">{{ Auth::user()->name }}</span>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0">
                        <li>
                            <a class="dropdown-item dropdown-item-danger d-flex align-items-center" href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión
                            </a>
                        </li>
                    </ul>
                </div>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            @endauth
        </div>
    </div>
</nav>

        {{-- Espaciado para el contenido --}}
        <main class="py-4">
            @yield('content')
        </main>
    </div>
</body>
</html>