@extends('layouts.app')

@section('content')
    <div class="container mx-auto mt-8 px-4">
        <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
            <div class="card-body p-4 d-flex justify-content-between align-items-center">
                <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
                    <div>
                        <h1 class="text-3xl font-extrabold text-slate-800"
                            style="font-size: 1.5rem; font-weight: 800; color: #1e293b;>Control de Accesos</h1>
                        <p class="text-slate-500">
                            Gestión de proveedores y perfiles del sistema</p>
                            <div class="audit-subtitle" style="font-size: 0.75rem; color: #64748b; font-weight: 700;">
                                <span class="pulse-indicator"></span> {{ $users->total() }} USUARIOS ENCONTRADOS
                            </div>
                    </div>
                    <div class="flex gap-3">
                        <div class="header-actions mt-2">
                            <a href="{{ route('home') }}" class="btn-ragon-outline">
                                <i class="fas fa-th-large me-2"></i> PANEL DE CONTROL
                            </a>
                        </div>
                        <a href="{{ route('users.create') }}" class="btn btn-gradient rounded-pill">
                            <i class="fas fa-user-plus"></i> NUEVO USUARIO
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
            <div class="card-body p-4">
                <form action="{{ route('users.index') }}" method="GET">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="d-flex flex-column flex-md-row gap-2">
                                <div class="search-container flex-grow-1 position-relative">
                                    <i
                                        class="fas fa-search text-muted position-absolute top-50 start-0 translate-middle-y ms-3"></i>
                                    <input type="text" name="search" class="search-input w-100 ps-5"
                                        placeholder="Buscar por nombre, email, ID o RFC..." value="{{ $search }}">
                                </div>
                                <button type="submit" class="btn btn-gradient px-4 py-2 rounded-pill fw-bold">
                                    <i class="fas fa-filter me-2"></i>Aplicar Filtros
                                </button>
                            </div>
                        </div>

                        <div class="col-12 col-md-6 col-lg-4">
                            <label class="form-label fw-semibold text-secondary small">Filtrar por Usuario</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i
                                        class="fas fa-user-circle text-muted"></i></span>
                                <input class="form-control bg-light border-start-0" list="usuariosList" name="user"
                                    placeholder="Escribe un nombre..." value="{{ request('user') }}">
                            </div>
                            <datalist id="usuariosList">
                                @foreach ($usuarios_filtro as $u)
                                    <option value="{{ $u->name }}">
                                @endforeach
                            </datalist>
                        </div>

                        <div class="col-12 col-md-6 col-lg-4">
                            <label class="form-label fw-semibold text-secondary small">Perfil / Rol</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-user-shield text-muted"></i>
                                </span>
                                <select name="role" class="form-select bg-light border-start-0">
                                    <option value="">Todos los roles</option>
                                    <option value="Administrador"
                                        {{ request('role') == 'Administrador' ? 'selected' : '' }}>
                                        Administrador
                                    </option>
                                    <option value="Proveedor" {{ request('role') == 'Proveedor' ? 'selected' : '' }}>
                                        Proveedor
                                    </option>
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


        <div class="bg-white shadow-xl rounded-b-xl overflow-hidden border border-slate-200">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">ID Proveedor</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Nombre y correo</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Perfil / Rol</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Alta en Sistema</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($users as $user)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-slate-600">
                                #{{ $user->id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-slate-900">{{ $user->name }}</div>
                                <div class="text-xs text-slate-500">{{ $user->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-3 py-1 text-xs font-bold rounded-full {{ $user->role == 'Administrador' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700' }}">
                                    {{ strtoupper($user->role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                {{ $user->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('users.edit', $user->id) }}"
                                        class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                        onsubmit="return confirm('¿Revocar acceso a este usuario?')">
                                        @csrf @method('DELETE')
                                        <button class="p-2 text-rose-600 hover:bg-rose-50 rounded-lg">
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

        <div class="mt-6">
            {{ $users->links() }}
        </div>
    </div>
@endsection
