@extends('layouts.app')

@section('content')
<div class="container-xl mt-4">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Registro de Errores del Sistema</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-vcenter card-table text-nowrap">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Usuario</th>
                            <th>Módulo</th>
                            <th>Detalle del Error</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($errores as $error)
                            <tr>
                                <td>{{ $error->created_at->format('d/m/Y H:i:s') }}</td>
                                <td>{{ $error->user ? $error->user->name : 'Usuario no logueado' }}</td>
                                <td><span class="badge bg-danger text-white">{{ $error->modulo }}</span></td>
                                <td class="text-wrap text-danger fw-bold">{{ $error->accion }}</td>
                                <td>{{ $error->ip }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No hay errores registrados en el sistema.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer d-flex align-items-center">
            {{ $errores->links() }}
        </div>
    </div>
</div>
@endsection