<nav class="navbar navbar-expand-md navbar-dark navbar-custom shadow-lg">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-bold" href="{{ url('/home') }}">
            <img src="{{ asset('img/logo.png') }}" alt="Logo" class="img-fluid mt-3"
                style="max-height: 50px; object-fit: contain;">
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                @auth
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}"
                            href="{{ route('home') }}">Inicio</a>
                    </li>

                    @if (in_array(Auth::user()->role, ['superadmin', 'admin']))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('users*') ? 'active' : '' }}"
                                href="{{ route('users.index') }}">Usuarios</a>
                        </li>
                    @endif

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

                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('errores*') ? 'active' : '' }}"
                            href="{{ route('errores.index') }}">Errores</a>
                    </li>
                @endauth
            </ul>

            <div class="d-flex align-items-center gap-3">
                @auth
                    <div class="dropdown">
                        <a class="text-decoration-none dropdown-toggle-nocaret d-flex align-items-center" 
                           href="#" 
                           role="button" 
                           data-bs-toggle="dropdown" 
                           aria-expanded="false">
                            
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold shadow-sm"
                                    style="width: 35px; height: 35px; font-size: 0.9rem; min-width: 35px; border: 1.5px solid rgba(255,255,255,0.2);">
                                    {{ strtoupper(substr(Auth::user()->CardName ?? 'U', 0, 1)) }}
                                </div>

                                <div class="text-start lh-1" style="min-width: 100px;">
                                    <span class="d-block fw-bold text-white mb-0" style="font-size: 0.85rem;">
                                        {{ Auth::user()->CardName ?? 'Usuario' }}
                                    </span>
                                    <small class="text-uppercase" style="font-size: 0.65rem; color: #94a3b8; letter-spacing: 0.5px;">
                                        {{ Auth::user()->role ?? 'Rol indefinido' }}
                                    </small>
                                </div>
                            </div>
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 mt-2">
                            <li>
                                <a class="dropdown-item d-flex align-items-center py-2 text-danger"
                                    href="{{ route('logout') }}"
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
    </div>
</nav>