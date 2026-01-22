@extends('layouts.app')

@section('content')
    <div class="container mx-auto mt-8">

        <div class="header-actions mt-5">
            <a href="{{ route('home') }}" class="btn-ragon-outline">
                <i class="fas fa-th-large me-2"></i> PANEL DE CONTROL
            </a>
        </div>
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Listado de Usuarios</h1>
        </div>



        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr
                        class="bg-gray-100 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        <th class="px-5 py-3">ID</th>
                        <th class="px-5 py-3">Nombre</th>
                        <th class="px-5 py-3">Email</th>
                        <th class="px-5 py-3">Fecha de Registro</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                            <td class="px-5 py-4 text-sm">{{ $user->id }}</td>
                            <td class="px-5 py-4 text-sm font-medium text-gray-900">{{ $user->name }}</td>
                            <td class="px-5 py-4 text-sm text-gray-600">{{ $user->email }}</td>
                            <td class="px-5 py-4 text-sm text-gray-600">
                                {{ $user->created_at->format('d/m/Y') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Enlaces de paginación --}}
        {{-- <div class="mt-4">
            {{ $users->links() }}
        </div> --}}
    </div>
@endsection
