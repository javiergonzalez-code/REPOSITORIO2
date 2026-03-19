@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="fas fa-trash-restore text-danger"></i> Papelera de Reciclaje</h2>
        <a href="{{ route('home') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver al Inicio</a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('papelera.index') }}" class="row align-items-end">
                <div class="col-md-4">
                    <label for="tipo" class="form-label fw-bold">Filtrar por elemento eliminado:</label>
                    <select name="tipo" id="tipo" class="form-select" onchange="this.form.submit()">
                        <option value="todos" {{ $filtro == 'todos' ? 'selected' : '' }}>Todos (Usuarios y Archivos)</option>
                        <option value="usuarios" {{ $filtro == 'usuarios' ? 'selected' : '' }}>Solo Usuarios</option>
                        <option value="archivos" {{ $filtro == 'archivos' ? 'selected' : '' }}>Solo Archivos</option>
                    </select>
                </div>
            </form>
        </div>
    </div>

    @if($filtro === 'todos' || $filtro === 'usuarios')
        <div class="card border-danger shadow-sm mb-4">
            <div class="card-header bg-danger text-white fw-bold">
                <i class="fas fa-users"></i> Usuarios Eliminados ({{ $usuarios->count() }})
            </div>
            <div class="card-body table-responsive">
                <table class="table table-hover table-vcenter">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Eliminado el</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($usuarios as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->deleted_at->format('d/m/Y H:i') }}</td>
                            <td class="text-end">
                                <form action="{{ route('papelera.restaurar', ['tipo' => 'usuario', 'id' => $user->id]) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-undo"></i> Restaurar</button>
                                </form>
                                <form action="{{ route('papelera.eliminar', ['tipo' => 'usuario', 'id' => $user->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar este usuario permanentemente?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-dark btn-sm"><i class="fas fa-times"></i> Destruir</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted">No hay usuarios en la papelera.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    @if($filtro === 'todos' || $filtro === 'archivos')
        <div class="card border-info shadow-sm mb-4">
            <div class="card-header bg-info text-white fw-bold">
                <i class="fas fa-file"></i> Archivos Eliminados ({{ $archivos->count() }})
            </div>
            <div class="card-body table-responsive">
                <table class="table table-hover table-vcenter">
                    <thead>
                        <tr>
                            <th>Archivo</th>
                            <th>Módulo</th>
                            <th>Subido por</th>
                            <th>Eliminado el</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($archivos as $archivo)
                        <tr>
                            <td>{{ $archivo->nombre_original }}</td>
                            <td><span class="badge bg-secondary">{{ $archivo->modulo }}</span></td>
                            <td>{{ $archivo->user->name ?? 'N/A' }}</td>
                            <td>{{ $archivo->deleted_at->format('d/m/Y H:i') }}</td>
                            <td class="text-end">
                                <form action="{{ route('papelera.restaurar', ['tipo' => 'archivo', 'id' => $archivo->id]) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-undo"></i> Restaurar</button>
                                </form>
                                <form action="{{ route('papelera.eliminar', ['tipo' => 'archivo', 'id' => $archivo->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de destruir este archivo de la base de datos?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-dark btn-sm"><i class="fas fa-times"></i> Destruir</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted">No hay archivos en la papelera.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection