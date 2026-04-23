@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-10">

                {{-- ENCABEZADO ESTANDARIZADO --}}
                <x-module-header icon="fas fa-id-badge" title="DETALLES DEL USUARIO"
                    subtitle="INFORMACIÓN DE: {{ strtoupper($user->CardName ?? 'SIN NOMBRE') }}" backRoute="{{ route('users.index') }}" />

                {{-- TARJETA DE DETALLES --}}
                <div class="card border-0 shadow-sm rounded-4 bg-white custom-card">
                    <div class="card-body p-4 p-md-5">

                        {{-- AVATAR Y CABECERA DEL PERFIL --}}
                        <div class="text-center mb-5">
                            <i class="fas fa-user-circle text-primary" style="font-size: 5.5rem;"></i>
                            {{-- CardName y E_Mail --}}
                            <h3 class="fw-bold mt-3 mb-1 text-dark">{{ $user->CardName ?? 'Usuario sin nombre' }}</h3>
                            <p class="text-muted mb-3">{{ $user->E_Mail ?? 'Sin correo' }}</p>
                            <span class="badge bg-primary rounded-pill px-4 py-2 text-uppercase shadow-sm"
                                style="letter-spacing: 1px; font-size: 0.75rem;">
                                <i class="fas fa-user-shield me-1"></i> {{ $user->role ?? 'N/A' }}
                            </span>
                        </div>

                        <h6 class="text-uppercase text-muted fw-bold mb-4 text-center"
                            style="font-size: 0.75rem; letter-spacing: 1px;">
                            <i class="fas fa-id-card me-2"></i>Información General
                        </h6>

                        <div class="row g-4 mb-4 justify-content-center">
                            
                            {{-- CardCode (Código de Usuario / ID) --}}
                            <div class="col-md-6">
                                <label class="form-label-custom">Código de Usuario</label>
                                <div class="input-group-modern">
                                    <i class="fas fa-fingerprint icon text-muted"></i>
                                    <input type="text" class="form-input fw-semibold text-secondary"
                                        value="{{ $user->CardCode }}" readonly
                                        style="background-color: #f8fafc; cursor: default;">
                                </div>
                            </div>

                            {{-- LicTradNum (Sustituye a RFC) --}}
                            <div class="col-md-6">
                                <label class="form-label-custom">RFC</label>
                                <div class="input-group-modern">
                                    <i class="fas fa-passport icon text-muted"></i>
                                    <input type="text" class="form-input fw-semibold text-secondary"
                                        value="{{ $user->LicTradNum ?? 'No registrado' }}" readonly
                                        style="background-color: #f8fafc; cursor: default;">
                                </div>
                            </div>
                        </div>

                        <h6 class="text-uppercase text-muted fw-bold mb-4 mt-5 text-center"
                            style="font-size: 0.75rem; letter-spacing: 1px;">
                            <i class="fas fa-address-book me-2"></i>Datos de Contacto
                        </h6>

                        <div class="row g-4 mb-4 justify-content-center">
                            
                            {{-- E_Mail (Sustituye a email) --}}
                            <div class="col-md-7">
                                <label class="form-label-custom">Correo Electrónico</label>
                                <div class="input-group-modern">
                                    <i class="fas fa-envelope icon text-muted"></i>
                                    <input type="text" class="form-input fw-semibold text-secondary"
                                        value="{{ $user->E_Mail ?? 'Sin correo' }}" readonly
                                        style="background-color: #f8fafc; cursor: default;">
                                </div>
                            </div>

                            {{-- Cellular (Sustituye a telefono) --}}
                            <div class="col-md-5">
                                <label class="form-label-custom">Teléfono</label>
                                <div class="input-group-modern">
                                    <i class="fas fa-phone icon text-muted"></i>
                                    <input type="text" class="form-input fw-semibold text-secondary"
                                        value="{{ $user->Cellular ?? 'No registrado' }}" readonly
                                        style="background-color: #f8fafc; cursor: default;">
                                </div>
                            </div>
                        </div>

                        {{-- SECCIÓN: ACTIVIDAD EN EL SISTEMA --}}
                        <h6 class="text-uppercase text-muted fw-bold mb-4 mt-5 text-center"
                            style="font-size: 0.75rem; letter-spacing: 1px;">
                            <i class="fas fa-history me-2"></i>Actividad
                        </h6>

                        <div class="row g-4 mb-4 justify-content-center">
                            <div class="col-md-12">
                                <label class="form-label-custom">Fecha de Registro en el Sistema</label>
                                <div class="input-group-modern">
                                    <i class="fas fa-calendar-alt icon text-muted"></i>
                                    <input type="text" class="form-input fw-semibold text-secondary"
                                        value="{{ $user->created_at ? $user->created_at->format('d/m/Y') . ' a las ' . $user->created_at->format('H:i') . ' hrs' : 'Fecha no disponible' }}"
                                        readonly style="background-color: #f8fafc; cursor: default;">
                                </div>
                            </div>
                        </div>

                        {{-- BOTONES DE ACCIÓN --}}
                        <div
                            class="d-flex flex-column flex-md-row justify-content-end align-items-center pt-4 mt-5 border-top gap-3">
                            <a href="{{ route('users.index') }}"
                                class="btn btn-light border fw-bold text-secondary px-4 py-2 rounded-pill">
                                <i class="fas fa-arrow-left me-1"></i> Volver a la Lista
                            </a>

                            {{-- El botón ya usaba CardCode, lo mantenemos igual --}}
                            <a href="{{ route('users.edit', $user->CardCode) }}"
                                class="btn btn-primary px-5 py-2 rounded-pill shadow-sm fw-bold">
                                <i class="fas fa-edit me-2"></i> Editar Usuario
                            </a>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection