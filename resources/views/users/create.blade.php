@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                
                <div class="card-header bg-gradient bg-primary text-white p-4 text-center">
                    <h3 class="fw-bold mb-1">Crear Cuenta</h3>
                    <p class="mb-0 opacity-75 small">Complete la información para registrar un nuevo usuario</p>
                </div>

                <div class="card-body p-4 p-md-5 bg-light bg-opacity-10">
                    <form method="POST" action="{{ route('users.store') }}">
                        @csrf

                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="text" class="form-control rounded-3 @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Nombre Completo" required autofocus>
                                    <label for="name"><i class="bi bi-person me-2"></i>Nombre Completo</label>
                                    @error('name')
                                        <div class="invalid-feedback ps-2">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control rounded-3 @error('id') is-invalid @enderror" id="id" name="id" value="{{ old('id') }}" placeholder="Código" required>
                                    <label for="id"><i class="bi bi-upc-scan me-2"></i>ID</label>
                                    @error('id')
                                        <div class="invalid-feedback ps-2">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control rounded-3 @error('rfc') is-invalid @enderror" id="rfc" name="rfc" value="{{ old('rfc') }}" placeholder="RFC">
                                    <label for="rfc"><i class="bi bi-card-heading me-2"></i>RFC (Opcional)</label>
                                    @error('rfc')
                                        <div class="invalid-feedback ps-2">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-7">
                                <div class="form-floating">
                                    <input type="email" class="form-control rounded-3 @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="nombre@ejemplo.com" required>
                                    <label for="email"><i class="bi bi-envelope me-2"></i>Correo Electrónico</label>
                                    @error('email')
                                        <div class="invalid-feedback ps-2">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-floating">
                                    <input type="text" class="form-control rounded-3 @error('telefono') is-invalid @enderror" id="telefono" name="telefono" value="{{ old('telefono') }}" placeholder="Teléfono">
                                    <label for="telefono"><i class="bi bi-telephone me-2"></i>Teléfono</label>
                                    @error('telefono')
                                        <div class="invalid-feedback ps-2">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-floating mb-4">
                            <select class="form-select rounded-3 @error('role') is-invalid @enderror" id="role" name="role" required>
                                <option value="" selected disabled>Seleccione una opción</option>
                                @foreach($roles as $roleOption)
                                    <option value="{{ $roleOption }}" {{ old('role') == $roleOption ? 'selected' : '' }}>{{ $roleOption }}</option>
                                @endforeach
                            </select>
                            <label for="role"><i class="bi bi-shield-lock me-2"></i>Rol de Usuario</label>
                            @error('role')
                                <div class="invalid-feedback ps-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4 text-muted opacity-25">

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="password" class="form-control rounded-3 @error('password') is-invalid @enderror" id="password" name="password" placeholder="Contraseña" required>
                                    <label for="password"><i class="bi bi-lock me-2"></i>Contraseña</label>
                                    @error('password')
                                        <div class="invalid-feedback ps-2">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="password" class="form-control rounded-3" id="password-confirm" name="password_confirmation" placeholder="Confirmar" required>
                                    <label for="password-confirm"><i class="bi bi-check-circle me-2"></i>Confirmar</label>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-5">
                            <a href="{{ route('users.index') }}" class="btn btn-light btn-lg text-secondary border fw-medium px-4">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg fw-bold px-5 shadow-sm">
                                <i class="bi bi-save2 me-2"></i>Guardar Usuario
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>

</style>
@endsection