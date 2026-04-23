@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-10">

                {{-- ENCABEZADO ESTANDARIZADO --}}
                <x-module-header icon="fas fa-user-plus" title="NUEVO USUARIO" subtitle="FORMULARIO DE REGISTRO DE MIEMBROS"
                    backRoute="{{ route('users.index') }}" />

                {{-- TARJETA DEL FORMULARIO --}}
                <div class="card border-0 shadow-sm rounded-4 bg-white custom-card">
                    <div class="card-body p-4 p-md-5">
                        <form method="POST" action="{{ route('users.store') }}">
                            @csrf

                            <h6 class="text-uppercase text-muted fw-bold mb-4 text-center"
                                style="font-size: 0.75rem; letter-spacing: 1px;">
                                <i class="fas fa-id-card me-2"></i>Información General
                            </h6>

                            <div class="row g-4 mb-4 justify-content-center">
                                
                                {{-- CardCode (Sustituye al ID) --}}
                                <div class="col-md-4">
                                    <label for="CardCode" class="form-label-custom">Código de Usuario</label>
                                    <div class="input-group-modern">
                                        <i class="fas fa-fingerprint icon"></i>
                                        <input type="text" class="form-input @error('CardCode') is-invalid @enderror"
                                            id="CardCode" name="CardCode" value="{{ old('CardCode') }}"
                                            placeholder="Ej. USR01" required autofocus>
                                    </div>
                                    @error('CardCode')
                                        <div class="error-msg text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- CardName (Sustituye a name) --}}
                                <div class="col-md-8">
                                    <label for="CardName" class="form-label-custom">Nombre Completo</label>
                                    <div class="input-group-modern">
                                        <i class="fas fa-user icon"></i>
                                        <input type="text" class="form-input @error('CardName') is-invalid @enderror"
                                            id="CardName" name="CardName" value="{{ old('CardName') }}"
                                            placeholder="Ej. Juan Pérez" required>
                                    </div>
                                    @error('CardName')
                                        <div class="error-msg text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- LicTradNum (Sustituye a rfc) --}}
                                <div class="col-md-6">
                                    <label for="LicTradNum" class="form-label-custom">RFC</label>
                                    <div class="input-group-modern">
                                        <i class="fas fa-passport icon"></i>
                                        <input type="text" class="form-input @error('LicTradNum') is-invalid @enderror"
                                            id="LicTradNum" name="LicTradNum" value="{{ old('LicTradNum') }}"
                                            placeholder="Clave RFC" maxlength="13">
                                    </div>
                                    @error('LicTradNum')
                                        <div class="error-msg text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    {{-- Espacio vacío para mantener el diseño en cuadrícula --}}
                                </div>
                            </div>

                            <h6 class="text-uppercase text-muted fw-bold mb-4 mt-5 text-center"
                                style="font-size: 0.75rem; letter-spacing: 1px;">
                                <i class="fas fa-shield-alt me-2"></i>Cuenta y Acceso
                            </h6>

                            <div class="row g-4 mb-4 justify-content-center">
                                
                                {{-- E_Mail (Sustituye a email) --}}
                                <div class="col-md-7">
                                    <label for="E_Mail" class="form-label-custom">Correo Electrónico</label>
                                    <div class="input-group-modern">
                                        <i class="fas fa-envelope icon"></i>
                                        <input type="email" class="form-input @error('E_Mail') is-invalid @enderror"
                                            id="E_Mail" name="E_Mail" value="{{ old('E_Mail') }}"
                                            placeholder="usuario@empresa.com" required>
                                    </div>
                                    @error('E_Mail')
                                        <div class="error-msg text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Cellular (Sustituye a telefono) --}}
                                <div class="col-md-5">
                                    <label for="Cellular" class="form-label-custom text-uppercase x-small fw-bold">Teléfono
                                        de Contacto</label>
                                    <div class="input-group-modern">
                                        <i class="fas fa-phone icon text-muted"></i>
                                        <input type="text" class="form-input @error('Cellular') is-invalid @enderror"
                                            id="Cellular" name="Cellular"
                                            value="{{ old('Cellular') }}"
                                            placeholder="Ej. 222 123 4567" maxlength="15">
                                    </div>
                                    @error('Cellular')
                                        <div class="error-msg text-danger small mt-1">
                                            <i class="fas fa-exclamation-circle me-1"></i> {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label for="role" class="form-label-custom">Rol de Usuario</label>
                                    <div class="input-group-modern">
                                        <i class="fas fa-user-tag icon"></i>
                                        <select class="form-input @error('role') is-invalid @enderror" id="role"
                                            name="role" required style="background-color: #f8fafc;">
                                            <option value="" selected disabled>Seleccione un nivel de acceso...
                                            </option>
                                            @foreach ($roles ?? ['superadmin', 'admin', 'proveedor'] as $roleOption)
                                                <option value="{{ $roleOption }}"
                                                    {{ old('role') == $roleOption ? 'selected' : '' }}>
                                                    {{ ucfirst($roleOption) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('role')
                                        <div class="error-msg text-danger small mt-1">{{ $message }}</div>
                                    @enderror
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
                                    @error('password')
                                        <div class="error-msg text-danger small mt-1">{{ $message }}</div>
                                    @enderror
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
                                <a href="{{ route('users.index') }}"
                                    class="btn btn-light border fw-bold text-secondary px-4 py-2 rounded-pill">
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