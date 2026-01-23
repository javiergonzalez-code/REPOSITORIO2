@extends('layouts.app')

@section('content')
<div class="container mx-auto mt-8 px-4">
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-800">Control de Accesos</h1>
            <p class="text-slate-500">Gestión de colaboradores y perfiles del sistema</p>
        </div>
        <div class="flex gap-3">
                <div class="header-actions mt-5">
                    <a href="{{ route('home') }}" class="btn-ragon-outline">
                        <i class="fas fa-th-large me-2"></i> PANEL DE CONTROL
                    </a>
                </div>
            <a href="{{ route('users.create') }}" class="btn btn-gradient px-5 py-3 rounded-pill fw-bold">
                <i class="fas fa-user-plus me-2"></i> NUEVO USUARIO
            </a>
        </div>
    </div>

    <div class="bg-white p-4 rounded-t-xl border-x border-t border-slate-200">
        <form action="{{ route('users.index') }}" method="GET" class="relative">
            <span class="absolute inset-y-0 left-0 flex align-items-center pl-3">
                <i class="fas fa-search text-slate-400"></i>
            </span>
            <input type="text" name="search" value="{{ $search }}" 
                   class="block w-full pl-10 pr-3 py-2 border border-slate-300 rounded-md leading-5 bg-slate-50 placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 sm:text-sm" 
                   placeholder="Buscar por nombre, email o ID de empleado...">
        </form>
    </div>

    <div class="bg-white shadow-xl rounded-b-xl overflow-hidden border border-slate-200">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">ID Empleado</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Colaborador</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Perfil / Rol</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Alta en Sistema</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($users as $user)
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-slate-600">
                        #{{ $user->matricula }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-semibold text-slate-900">{{ $user->name }}</div>
                        <div class="text-xs text-slate-500">{{ $user->email }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-3 py-1 text-xs font-bold rounded-full {{ $user->role == 'Administrador' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700' }}">
                            {{ strtoupper($user->role) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                        {{ $user->created_at->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('users.edit', $user->id) }}" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('¿Revocar acceso a este usuario?')">
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