@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-10">

                {{-- ENCABEZADO ESTANDARIZADO --}}
                <x-module-header 
                    icon="fas fa-user-plus" 
                    title="NUEVO USUARIO" 
                    subtitle="FORMULARIO DE REGISTRO DE MIEMBROS"
                    backRoute="{{ route('users.index') }}"
                />

                {{-- TARJETA DEL FORMULARIO --}}
                <div class="card border-0 shadow-sm rounded-4 bg-white custom-card">
                    <div class="card-body p-4 p-md-5">
                        <form method="POST" action="{{ route('users.store') }}">
                            @csrf

                            <h6 class="text-uppercase text-muted fw-bold mb-4 text-center" style="font-size: 0.75rem; letter-spacing: 1px;">
                                <i class="fas fa-id-card me-2"></i>Información General
                            </h6>

                            <div class="row g-4 mb-4 justify-content-center">
                                <div class="col-12">
                                    <label for="name" class="form-label-custom">Nombre Completo</label>
                                    <div class="input-group-modern">
                                        <i class="fas fa-user icon"></i>
                                        <input type="text" class="form-input @error('name') is-invalid @enderror"
                                            id="name" name="name" value="{{ old('name') }}"
                                            placeholder="Ej. Juan Pérez" required autofocus>
                                    </div>
                                    @error('name') <div class="error-msg text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="id" class="form-label-custom">ID / Código de Empleado</label>
                                    <div class="input-group-modern">
                                        <i class="fas fa-hashtag icon"></i>
                                        <input type="text" class="form-input @error('id') is-invalid @enderror"
                                            id="id" name="id" value="{{ old('id') }}"
                                            placeholder="Ej. EMP-001" required>
                                    </div>
                                    @error('id') <div class="error-msg text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="rfc" class="form-label-custom">RFC</label>
                                    <div class="input-group-modern">
                                        <i class="fas fa-passport icon"></i>
                                        <input type="text" class="form-input @error('rfc') is-invalid @enderror"
                                            id="rfc" name="rfc" value="{{ old('rfc') }}" placeholder="Clave RFC">
                                    </div>
                                    @error('rfc') <div class="error-msg text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <h6 class="text-uppercase text-muted fw-bold mb-4 mt-5 text-center" style="font-size: 0.75rem; letter-spacing: 1px;">
                                <i class="fas fa-shield-alt me-2"></i>Cuenta y Acceso
                            </h6>

                            <div class="row g-4 mb-4 justify-content-center">
                                <div class="col-md-7">
                                    <label for="email" class="form-label-custom">Correo Electrónico</label>
                                    <div class="input-group-modern">
                                        <i class="fas fa-envelope icon"></i>
                                        <input type="email" class="form-input @error('email') is-invalid @enderror"
                                            id="email" name="email" value="{{ old('email') }}"
                                            placeholder="usuario@empresa.com" required>
                                    </div>
                                    @error('email') <div class="error-msg text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-5">
                                    <label for="telefono" class="form-label-custom">Teléfono</label>
                                    <div class="input-group-modern">
                                        <i class="fas fa-phone icon"></i>
                                        <input type="text" class="form-input @error('telefono') is-invalid @enderror"
                                            id="telefono" name="telefono" value="{{ old('telefono') }}"
                                            placeholder="55 1234 5678">
                                    </div>
                                    @error('telefono') <div class="error-msg text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-12">
                                    <label for="role" class="form-label-custom">Rol de Usuario</label>
                                    <div class="input-group-modern">
                                        <i class="fas fa-user-tag icon"></i>
                                        <select class="form-input @error('role') is-invalid @enderror" id="role"
                                            name="role" required style="background-color: #f8fafc;">
                                            <option value="" selected disabled>Seleccione un nivel de acceso...</option>
                                            @foreach ($roles ?? ['superadmin', 'admin', 'proveedor'] as $roleOption)
                                                <option value="{{ $roleOption }}" {{ old('role') == $roleOption ? 'selected' : '' }}>
                                                    {{ ucfirst($roleOption) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('role') <div class="error-msg text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="row g-4 mb-5 justify-content-center">
                                <div class="col-md-6">
                                    <label for="password" class="form-label-custom">Contraseña</label>
                                    <div class="input-group-modern">
                                        <i class="fas fa-lock icon"></i>
                                        <input type="password" class="form-input @error('password') is-invalid @enderror"
                                            id="password" name="password" placeholder="********" required>
                                    </div>
                                    @error('password') <div class="error-msg text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="password-confirm" class="form-label-custom">Confirmar Contraseña</label>
                                    <div class="input-group-modern">
                                        <i class="fas fa-check-circle icon"></i>
                                        <input type="password" class="form-input" id="password-confirm"
                                            name="password_confirmation" placeholder="********" required>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex flex-column flex-md-row justify-content-end align-items-center pt-4 mt-4 border-top gap-3">
                                <a href="{{ route('users.index') }}" class="btn btn-light border fw-bold text-secondary px-4 py-2 rounded-pill">
                                    Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary px-5 py-2 rounded-pill shadow-sm fw-bold">
                                    <i class="fas fa-save me-2"></i> Guardar Usuario
                                </button>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection