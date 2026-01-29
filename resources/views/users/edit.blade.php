@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                
                <div class="card-header bg-gradient bg-primary text-white p-4">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-person-gear fs-1 me-3 opacity-75"></i>
                        <div>
                            <h3 class="fw-bold mb-1">Editar Usuario</h3>
                            <p class="mb-0 opacity-75 small">Actualizando información de: <strong>{{ $user->name }}</strong></p>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4 p-md-5 bg-light bg-opacity-10">
                    <form method="POST" action="{{ route('users.update', $user->id) }}">
                        @csrf
                        @method('PUT')

                        <h6 class="fw-bold text-muted mb-3"><i class="bi bi-info-circle me-2"></i>Información General</h6>
                        <div class="form-floating mb-4">
                            <input type="text" class="form-control rounded-3 @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" placeholder="Nombre Completo" required>
                            <label for="name"><i class="bi bi-person me-2"></i>Nombre Completo</label>
                            @error('name')
                                <div class="invalid-feedback ps-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control rounded-3 @error('codigo') is-invalid @enderror" id="codigo" name="codigo" value="{{ old('codigo', $user->codigo) }}" placeholder="Código" required>
                                    <label for="codigo"><i class="bi bi-upc-scan me-2"></i>Código</label>
                                    @error('codigo')
                                        <div class="invalid-feedback ps-2">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control rounded-3 @error('rfc') is-invalid @enderror" id="rfc" name="rfc" value="{{ old('rfc', $user->rfc) }}" placeholder="RFC">
                                    <label for="rfc"><i class="bi bi-card-heading me-2"></i>RFC (Opcional)</label>
                                    @error('rfc')
                                        <div class="invalid-feedback ps-2">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr class="my-4 text-muted opacity-25">

                        <h6 class="fw-bold text-muted mb-3"><i class="bi bi-diagram-3 me-2"></i>Contacto y Permisos</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-7">
                                <div class="form-floating">
                                    <input type="email" class="form-control rounded-3 @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" placeholder="nombre@ejemplo.com" required>
                                    <label for="email"><i class="bi bi-envelope me-2"></i>Correo Electrónico</label>
                                    @error('email')
                                        <div class="invalid-feedback ps-2">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-floating">
                                    <input type="text" class="form-control rounded-3 @error('telefono') is-invalid @enderror" id="telefono" name="telefono" value="{{ old('telefono', $user->telefono) }}" placeholder="Teléfono">
                                    <label for="telefono"><i class="bi bi-telephone me-2"></i>Teléfono</label>
                                    @error('telefono')
                                        <div class="invalid-feedback ps-2">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-floating mb-4">
                            <select class="form-select rounded-3 @error('role') is-invalid @enderror" id="role" name="role" required>
                                <option value="" disabled>Seleccione una opción</option>
                                @foreach($roles as $roleOption)
                                    <option value="{{ $roleOption }}" {{ (old('role', $user->role) == $roleOption) ? 'selected' : '' }}>{{ $roleOption }}</option>
                                @endforeach
                            </select>
                            <label for="role"><i class="bi bi-shield-lock me-2"></i>Rol de Usuario</label>
                            @error('role')
                                <div class="invalid-feedback ps-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4 text-muted opacity-25">

                        <h6 class="fw-bold text-muted mb-3">
                            <i class="bi bi-lock me-2"></i>Seguridad 
                            <span class="badge bg-info text-dark fw-normal ms-2 small">Opcional</span>
                        </h6>
                        <div class="alert alert-light border-primary border-opacity-25 shadow-sm mb-3 small text-primary">
                            <i class="bi bi-info-circle-fill me-1"></i>
                            Solo llena estos campos si deseas cambiar la contraseña actual del usuario.
                        </div>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="password" class="form-control rounded-3 @error('password') is-invalid @enderror" id="password" name="password" placeholder="Nueva Contraseña">
                                    <label for="password">Nueva Contraseña</label>
                                    @error('password')
                                        <div class="invalid-feedback ps-2">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="password" class="form-control rounded-3" id="password-confirm" name="password_confirmation" placeholder="Confirmar Nueva">
                                    <label for="password-confirm">Confirmar Nueva</label>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-5">
                            <a href="{{ route('users.index') }}" class="btn btn-light btn-lg text-secondary border fw-medium px-4">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg fw-bold px-4 shadow-sm">
                                <i class="bi bi-arrow-repeat me-2"></i>Actualizar Usuario
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection