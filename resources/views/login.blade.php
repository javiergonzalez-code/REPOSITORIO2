@extends('layouts.app')

@section('content')
<div class="login-wrapper">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-11 col-sm-8 col-md-6 col-lg-4">
                
                <div class="login-wrapper">
    <div class="card login-card">
        <div class="card-header login-header-modern">
            <div class="brand-icon-wrapper">
                <i class="fas fa-shield-alt"></i>
            </div>
            <h2 class="h4 fw-bold mb-0 mt-3">REPOSITORIO</h2>
            <span class="badge bg-blue-ragon mt-2">SISTEMA INTERNO</span>
        </div>

        <div class="card-body p-4 p-md-5">
            <form method="POST" action="{{ route('login.post') }}">
                @csrf
                
                <div class="mb-4">
                    <label class="form-label-custom d-block text-center">CORREO ELECTRÓNICO</label>
                    <div class="input-group-modern">
                        <i class="fas fa-envelope icon"></i>
                        <input type="email" name="email" class="form-input text-center" placeholder="usuario@ragon.com.mx" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label-custom d-block text-center">CONTRASEÑA</label>
                    <div class="input-group-modern">
                        <i class="fas fa-lock icon"></i>
                        <input type="password" name="password" class="form-input text-center" placeholder="••••••••" required>
                    </div>
                </div>

                <div class="d-flex justify-content-center mb-4">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="remember">
                        <label class="form-check-label text-muted small" for="remember">Mantener sesión</label>
                    </div>
                </div>

                <button type="submit" class="btn-ragon-modern w-100">
                    INICIAR SESIÓN
                </button>
            </form>
        </div>
    </div>
</div>
                
            </div>
        </div>
    </div>
</div>
@endsection