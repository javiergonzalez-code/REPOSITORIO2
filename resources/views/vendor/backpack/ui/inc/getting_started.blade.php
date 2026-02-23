<div class="card">
    <div class="card-header border-0">
        <div class="d-flex justify-content-between align-items-center">
            {{-- Saludo Personalizado --}}
            <h3 class="mb-0">
                <i class="la la-user-shield text-primary me-2"></i>
                Bienvenido al Panel de Control, {{ backpack_user()->name ?? 'Superusuario' }}
            </h3>
            <span class="badge bg-primary">Super Usuario</span>
        </div>
    </div>

    <div class="card-body">
        <p class="lead">
            Has iniciado sesión con privilegios elevados. Desde este panel puedes gestionar usuarios, controlar la seguridad y supervisar los registros del sistema.
        </p>
        <hr>

        {{-- Sección de Accesos Rápidos --}}
        <h5 class="card-title text-muted mb-3">Accesos Rápidos al Sistema</h5>
        
        <div class="row">
            {{-- Tarjeta: Gestión de Usuarios --}}
            <div class="col-md-4 mb-3">
                <div class="card h-100 border-primary border-start-0 border-end-0 border-top-0 border-3 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <i class="la la-users la-2x text-primary me-2"></i>
                            <h4 class="mb-0">Usuarios</h4>
                        </div>
                        <p class="card-text text-muted small">Administra las cuentas de acceso, crea nuevos usuarios y actualiza perfiles.</p>
                        <a href="{{ backpack_url('user') }}" class="btn btn-sm btn-outline-primary w-100 stretched-link">Gestionar Usuarios</a>
                    </div>
                </div>
            </div>

            {{-- Tarjeta: Seguridad (Roles y Permisos) --}}
            <div class="col-md-4 mb-3">
                <div class="card h-100 border-success border-start-0 border-end-0 border-top-0 border-3 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <i class="la la-key la-2x text-success me-2"></i>
                            <h4 class="mb-0">Seguridad</h4>
                        </div>
                        <p class="card-text text-muted small">Configura Roles y Permisos para definir quién puede acceder a qué partes del sistema.</p>
                        <div class="d-flex gap-2 mt-auto">
                            <a href="{{ backpack_url('role') }}" class="btn btn-sm btn-outline-success flex-fill" style="z-index: 2;">Roles</a>
                            <a href="{{ backpack_url('permission') }}" class="btn btn-sm btn-outline-success flex-fill" style="z-index: 2;">Permisos</a>
                        </div>
                    </div>
                </div>
            </div>

             {{-- Tarjeta: Logs y Archivos --}}
             <div class="col-md-4 mb-3">
                <div class="card h-100 border-info border-start-0 border-end-0 border-top-0 border-3 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <i class="la la-server la-2x text-info me-2"></i>
                            <h4 class="mb-0">Sistema</h4>
                        </div>
                        <p class="card-text text-muted small">Revisa los registros de actividad (Logs) y gestiona los archivos subidos.</p>
                         <div class="d-flex gap-2 mt-auto">
                            <a href="{{ backpack_url('log') }}" class="btn btn-sm btn-outline-info flex-fill" style="z-index: 2;">Logs</a>
                            <a href="{{ backpack_url('archivo') }}" class="btn btn-sm btn-outline-info flex-fill" style="z-index: 2;">Archivos</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Alertas o Estado del Sistema (Opcional) --}}
        <div class="alert alert-light border mt-3" role="alert">
            <h5 class="alert-heading"><i class="la la-info-circle"></i> Información Importante</h5>
            <p class="mb-0 small">
                Recuerda que cualquier cambio realizado en la configuración de <strong>Roles y Permisos</strong> afectará inmediatamente a la seguridad de la aplicación. Actúa con precaución al eliminar registros.
            </p>
        </div>
    </div>
</div>