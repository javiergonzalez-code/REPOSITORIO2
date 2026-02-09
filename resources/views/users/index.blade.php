@extends('layouts.app')

@section('content')
    <div class="container mx-auto mt-8 px-4">
        {{-- Header: Control de Accesos --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4 custom-card">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-4">
                    <div>
                        <h1 class="audit-title mb-0" style="font-size: 1.5rem; font-weight: 800;">Control de Accesos</h1>
                        <p class="text-muted mb-2">Gestión de proveedores y perfiles del sistema</p>
                        <div class="audit-subtitle">
                            <span class="pulse-indicator"></span> {{ $users->total() }} USUARIOS ENCONTRADOS
                        </div>
                    </div>
                    <div class="d-flex gap-3 align-items-center">
                        <a href="{{ route('home') }}" class="btn-ragon-outline">
                            <i class="fas fa-th-large me-2"></i> PANEL DE CONTROL
                        </a>
                        <a href="{{ route('users.create') }}" class="btn btn-gradient rounded-pill">
                            <i class="fas fa-user-plus me-2"></i> NUEVO USUARIO
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Barra de Búsqueda y Filtros --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4 custom-card">
            <div class="card-body p-4">
                <form action="{{ route('users.index') }}" method="GET">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="d-flex flex-column flex-md-row gap-2">
                                <div class="search-container flex-grow-1 position-relative">
                                    <i class="fas fa-search text-muted position-absolute top-50 start-0 translate-middle-y ms-3"></i>
                                    <input type="text" name="search" class="search-input w-100 ps-5"
                                        placeholder="Buscar por nombre, email, ID o RFC..." value="{{ $search }}">
                                </div>
                                <button type="submit" class="btn btn-gradient px-4 py-2 rounded-pill fw-bold">
                                    <i class="fas fa-filter me-2"></i>Aplicar Filtros
                                </button>
                            </div>
                        </div>

                        <div class="col-12 col-md-6 col-lg-4">
                            <label class="form-label-custom small">Filtrar por Usuario</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="fas fa-user-circle text-muted"></i></span>
                                <input class="form-input" list="usuariosList" name="user"
                                    placeholder="Escribe un nombre..." value="{{ request('user') }}">
                            </div>
                            <datalist id="usuariosList">
                                @foreach ($usuarios_filtro as $u)
                                    <option value="{{ $u->name }}">
                                @endforeach
                            </datalist>
                        </div>

                        <div class="col-12 col-md-6 col-lg-4">
                            <label class="form-label-custom small">Perfil / Rol</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="fas fa-user-shield text-muted"></i></span>
                                <select name="role" class="form-input">
                                    <option value="">Todos los roles</option>
                                    <option value="Administrador" {{ request('role') == 'Administrador' ? 'selected' : '' }}>Administrador</option>
                                    <option value="Proveedor" {{ request('role') == 'Proveedor' ? 'selected' : '' }}>Proveedor</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-12 col-lg-4 d-flex align-items-end">
                            <a href="{{ route('users.index') }}" class="btn btn-link text-decoration-none text-muted small">
                                <i class="fas fa-eraser me-1"></i>Limpiar filtros
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tabla de Usuarios --}}
        <div class="card border-0 shadow-sm rounded-4 custom-card overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr class="text-uppercase" style="font-size: 0.7rem; font-weight: 800; letter-spacing: 0.5px;">
                            <th class="ps-4 py-3">ID Proveedor</th>
                            <th class="py-3">Nombre y correo</th>
                            <th class="py-3">Perfil / Rol</th>
                            <th class="py-3">Alta en Sistema</th>
                            <th class="text-end pe-4 py-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr class="log-row">
                                <td class="ps-4">
                                    <span class="font-monospace text-muted small">#{{ $user->id }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold text-main">{{ $user->name }}</div>
                                    <div class="x-small text-muted">{{ $user->email }}</div>
                                </td>
                                <td>
                                    <span class="status-indicator {{ $user->role == 'Administrador' ? 'status-error' : 'status-upload' }} d-inline-flex">
                                        <span class="dot"></span> {{ strtoupper($user->role) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-main small">{{ $user->created_at->format('M d, Y') }}</span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-light border rounded-3 text-primary shadow-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('¿Revocar acceso a este usuario?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-light border rounded-3 text-danger shadow-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4 d-flex justify-content-center">
            {{ $users->links() }}
        </div>
    </div>
@endsection