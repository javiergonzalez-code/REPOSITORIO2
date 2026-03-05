<nav class="navbar navbar-expand-md navbar-dark navbar-custom shadow-lg">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-bold" href="{{ url('/home') }}">
            <img src="{{ asset('img/logo.png') }}" alt="Logo" class="img-fluid mt-3"
                style="max-height: 50px; object-fit: contain;">
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
                            <a class="nav-link {{ request()->is('users*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                                <i class="fas me-1"></i> Usuarios
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
                            href="{{ route('logs.index') }}">Logs</a>
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
                    <a class="nav-link text-white d-flex align-items-center" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle fa-lg me-2"></i>
                        <span class="d-none d-sm-inline">{{ Auth::user()->name }}</span>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item dropdown-item-danger d-flex align-items-center"
                                href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión
                            </a>
                        </li>
                    </ul>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                @endauth
            </div>
        </div>
    </div>
</nav>
