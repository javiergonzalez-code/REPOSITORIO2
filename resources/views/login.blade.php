@extends('layouts.app')

@section('content')
    <div class="login-wrapper">
        <div class="container">
            <div class="row justify-content-center align-items-center min-vh-100">
                <div>

                    <div class="login-wrapper">
                        <div class="card login-card">
                            <div class="card-header login-header-modern">
                                <img src="{{ asset('img/logo.png') }}" alt="Logo" class="img-fluid mt-3" style="max-height: 150px; object-fit: contain;">
                            </div>

                            <div class="card-body p-4 p-md-5">
                                <form method="POST" action="{{ route('login.post') }}">
                                    @csrf

                                    <div class="mb-4">
                                        <label class="form-label-custom d-block text-center">CORREO ELECTRÓNICO</label>
                                        <div class="input-group-modern">
                                            <i class="fas fa-envelope icon"></i>
                                            <input type="email" name="email" class="form-input text-center"
                                                placeholder="usuario@ragon.com.mx" required>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label-custom d-block text-center">CONTRASEÑA</label>
                                        <div class="input-group-modern">
                                            <i class="fas fa-lock icon"></i>
                                            <input type="password" name="password" class="form-input text-center"
                                                placeholder="••••••••" required>
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