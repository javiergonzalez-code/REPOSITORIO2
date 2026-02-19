<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'REPOSITORIO') }}</title>

    {{-- FontAwesome para los iconos --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Carga de CSS y JS con Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Estilos rápidos para el modo oscuro manual si no los tienes en app.css */
        body.dark-mode {
            background-color: #1a1a1a;
            color: #e0e0e0;
        }

        .navbar-custom {
            background-color: #2c3e50;
        }

        body.dark-mode .card {
            background-color: #2d2d2d;
            color: white;
            border: 1px solid #444;
        }

        .dropdown-item-danger {
            color: #dc3545;
        }

        .dropdown-item-danger:hover {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>

<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-dark navbar-custom shadow-lg">
            <div class="container-fluid px-4">
                <a class="navbar-brand fw-bold" href="{{ url('/home') }}">
                    <i class="fas fa-database me-2"></i> REPOSITORIO
                </a>

                {{-- Botón de Hamburguesa para Móvil --}}
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNav">
                    {{-- LADO IZQUIERDO: Enlaces de navegación protegidos --}}
                    <ul class="navbar-nav me-auto">
                        @auth
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}"
                                    href="{{ route('home') }}">Inicio</a>
                            </li>

                            {{-- IMPLEMENTACIÓN DE ROLES Y PERMISOS --}}
                            @can('list users')
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->is('users*') ? 'active' : '' }}"
                                        href="{{ route('users.index') }}">
                                        <i class="fas fa-users me-1"></i> Usuarios
                                    </a>
                                </li>
                            @endcan

                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('oc*') ? 'active' : '' }}"
                                    href="{{ route('oc.index') }}">Órdenes de Compra</a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('inputs*') ? 'active' : '' }}"
                                    href="{{ route('input.index') }}">Carga Archivos</a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('logs*') ? 'active' : '' }}"
                                    href="{{ route('logs.index') }}">Auditoría</a>
                            </li>
                        @endauth
                    </ul>

                    {{-- LADO DERECHO: Herramientas y Usuario --}}
                    <div class="d-flex align-items-center gap-3">
                        {{-- Botón Modo Oscuro --}}
                        <button id="dark-mode-toggle" class="btn btn-link text-white p-0 shadow-none">
                            <i class="fas fa-moon" id="dark-mode-icon"></i>
                        </button>

                        @auth

                            <a class="nav-link  text-white d-flex align-items-center" href="#"
                                role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle fa-lg me-2"></i>
                                <span class="d-none d-sm-inline">{{ Auth::user()->name }}</span>
                            </a>

                            <a class="dropdown-item dropdown-item-danger d-flex align-items-center"
                                href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt me-2"></i> Cerrar
                                Sesión
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <main class="py-4 container">
            @yield('content')
        </main>
    </div>

    {{-- Scripts
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Lógica simple para el Modo Oscuro
        const btn = document.getElementById('dark-mode-toggle');
        const icon = document.getElementById('dark-mode-icon');
        const body = document.body;

        // Cargar preferencia guardada
        if (localStorage.getItem('theme') === 'dark') {
            body.classList.add('dark-mode');
            icon.classList.replace('fa-moon', 'fa-sun');
        }

        btn.addEventListener('click', () => {
            body.classList.toggle('dark-mode');
            if (body.classList.contains('dark-mode')) {
                icon.classList.replace('fa-moon', 'fa-sun');
                localStorage.setItem('theme', 'dark');
            } else {
                icon.classList.replace('fa-sun', 'fa-moon');
                localStorage.setItem('theme', 'light');
            }
        });
    </script> --}}
</body>

</html>
