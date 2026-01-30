@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            
            {{-- Tarjeta Principal Estilo Ragon --}}
            <div class="card login-card shadow-lg border-0 overflow-hidden">
                
                {{-- Header Moderno --}}
                <div class="login-header-modern py-4">
                    <div class="d-flex justify-content-center mb-3">
                        <div class="brand-icon-wrapper" style="width: 60px; height: 60px; font-size: 1.8rem;">
                            <i class="fas fa-user-edit"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-1 text-white text-uppercase" style="letter-spacing: 1px;">Editar Usuario</h3>
                    <p class="text-white-50 mb-0" style="font-size: 0.9rem;">
                        Actualizando información de: <strong class="text-white">{{ $user->name }}</strong>
                    </p>
                </div>

                <div class="card-body p-4 p-md-5">
                    <form method="POST" action="{{ route('users.update', $user->id) }}">
                        @csrf
                        @method('PUT')

                        {{-- Sección: Información General --}}
                        <h6 class="text-uppercase text-muted fw-bold mb-4" style="font-size: 0.75rem; letter-spacing: 1px;">
                            <i class="fas fa-id-card me-2"></i>Datos Personales
                        </h6>

                        <div class="row g-4 mb-4">
                            {{-- Nombre Completo --}}
                            <div class="col-12">
                                <label for="name" class="form-label-custom">Nombre Completo</label>
                                <div class="input-group-modern">
                                    <i class="fas fa-user icon"></i>
                                    <input type="text" class="form-input @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $user->name) }}" 
                                           placeholder="Nombre Completo" required>
                                </div>
                                @error('name')
                                    <div class="error-msg">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Código y RFC --}}
                            {{-- <div class="col-md-6">
                                <label for="codigo" class="form-label-custom">Código / ID</label>
                                <div class="input-group-modern">
                                    <i class="fas fa-hashtag icon"></i>
                                    <input type="text" class="form-input @error('codigo') is-invalid @enderror" 
                                           id="codigo" name="codigo" value="{{ old('codigo', $user->codigo) }}" 
                                           placeholder="Código de Usuario" required>
                                </div>
                                @error('codigo')
                                    <div class="error-msg">{{ $message }}</div>
                                @enderror
                            </div> --}}

                            <div class="col-md-6">
                                <label for="rfc" class="form-label-custom">RFC</label>
                                <div class="input-group-modern">
                                    <i class="fas fa-passport icon"></i>
                                    <input type="text" class="form-input @error('rfc') is-invalid @enderror" 
                                           id="rfc" name="rfc" value="{{ old('rfc', $user->rfc) }}" 
                                           placeholder="RFC">
                                </div>
                                @error('rfc')
                                    <div class="error-msg">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Sección: Contacto y Roles --}}
                        <h6 class="text-uppercase text-muted fw-bold mb-4 mt-5" style="font-size: 0.75rem; letter-spacing: 1px;">
                            <i class="fas fa-shield-alt me-2"></i>Contacto y Permisos
                        </h6>

                        <div class="row g-4 mb-4">
                            {{-- Email --}}
                            <div class="col-md-7">
                                <label for="email" class="form-label-custom">Correo Electrónico</label>
                                <div class="input-group-modern">
                                    <i class="fas fa-envelope icon"></i>
                                    <input type="email" class="form-input @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email', $user->email) }}" 
                                           placeholder="correo@ejemplo.com" required>
                                </div>
                                @error('email')
                                    <div class="error-msg">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Teléfono --}}
                            <div class="col-md-5">
                                <label for="telefono" class="form-label-custom">Teléfono</label>
                                <div class="input-group-modern">
                                    <i class="fas fa-phone icon"></i>
                                    <input type="text" class="form-input @error('telefono') is-invalid @enderror" 
                                           id="telefono" name="telefono" value="{{ old('telefono', $user->telefono) }}" 
                                           placeholder="Teléfono">
                                </div>
                                @error('telefono')
                                    <div class="error-msg">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Rol --}}
                            <div class="col-12">
                                <label for="role" class="form-label-custom">Rol de Usuario</label>
                                <div class="input-group-modern">
                                    <i class="fas fa-user-tag icon"></i>
                                    <select class="form-input @error('role') is-invalid @enderror" 
                                            id="role" name="role" required style="background-color: #f8fafc;">
                                        <option value="" disabled>Seleccione una opción</option>
                                        @foreach($roles as $roleOption)
                                            <option value="{{ $roleOption }}" 
                                                {{ (old('role', $user->role) == $roleOption) ? 'selected' : '' }}>
                                                {{ $roleOption }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('role')
                                    <div class="error-msg">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Sección: Seguridad (Contraseña) --}}
                        <div class="d-flex align-items-center mt-5 mb-4">
                            <h6 class="text-uppercase text-muted fw-bold mb-0 me-3" style="font-size: 0.75rem; letter-spacing: 1px;">
                                <i class="fas fa-lock me-2"></i>Seguridad
                            </h6>
                            <span class="badge bg-info-light text-primary border border-info px-3 rounded-pill" style="font-size: 0.7rem;">
                                Opcional
                            </span>
                        </div>

                        {{-- Alerta informativa estilo Ragon --}}
                        <div class="alert alert-custom alert-general-ragon mb-4" role="alert" 
                             style="background-color: #f0f9ff; border-left: 4px solid #3b82f6; border-radius: 4px;">
                            <div class="d-flex align-items-center text-primary">
                                <i class="fas fa-info-circle fa-lg me-3"></i>
                                <span class="small fw-semibold">Solo rellene estos campos si desea cambiar la contraseña actual.</span>
                            </div>
                        </div>

                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label for="password" class="form-label-custom">Nueva Contraseña</label>
                                <div class="input-group-modern">
                                    <i class="fas fa-key icon"></i>
                                    <input type="password" class="form-input @error('password') is-invalid @enderror" 
                                           id="password" name="password" placeholder="Nueva contraseña">
                                </div>
                                @error('password')
                                    <div class="error-msg">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="password-confirm" class="form-label-custom">Confirmar Nueva</label>
                                <div class="input-group-modern">
                                    <i class="fas fa-check-double icon"></i>
                                    <input type="password" class="form-input" 
                                           id="password-confirm" name="password_confirmation" placeholder="Repetir contraseña">
                                </div>
                            </div>
                        </div>

                        {{-- Botones de Acción (Con la separación arreglada) --}}
                        <div class="d-flex justify-content-end align-items-center pt-4 mt-5 border-top">
                            
                            {{-- Botón Cancelar (con margen derecho extra 'me-5' para separar) --}}
                            <a href="{{ route('users.index') }}" 
                               class="btn btn-light border fw-bold text-secondary px-4 py-3 rounded-3 me-5">
                                Cancelar
                            </a>

                            {{-- Botón Guardar --}}
                            <button type="submit" class="btn btn-ragon-modern px-5 py-3 shadow-lg" style="width: auto;">
                                <span>Actualizar Usuario</span>
                                <i class="fas fa-sync-alt ms-3"></i>
                            </button>
                            
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection