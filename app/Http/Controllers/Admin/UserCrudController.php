<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\UserRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class UserCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\SoftDeleteOperation;

    public function setup()
    {
        $this->crud->setModel(\App\Models\User::class);
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/user');
        $this->crud->setEntityNameStrings('usuario', 'usuarios');

        $user = backpack_user();

        if ($user->role !== 'superadmin' && $user->email !== 'admin@ragon.com') {
            CRUD::denyAccess(['list', 'show', 'create', 'update', 'delete']);
        }
    }

    protected function setupListOperation()
    {
        $user = backpack_user();

        // Filtro de visibilidad: El admin normal NO ve a los superadmins en la lista
        if ($user->role !== 'superadmin' && $user->email !== 'admin@ragon.com') {
            $this->crud->addClause('where', 'role', '!=', 'superadmin');
        }

        CRUD::column('id')->label('ID');
        CRUD::column('name')->label('Nombre');
        CRUD::column('email')->label('Correo');
        CRUD::column('role')->label('Rol Base');
        CRUD::column('created_at')->label('Creado')->type('date');

        // ==========================================
        // FILTROS MANUALES (Compatibles con versión Free)
        // ==========================================

        // Filtrar por Nombre
        if (request()->has('custom_name') && request()->filled('custom_name')) {
            $this->crud->addClause('where', 'name', 'like', '%' . request()->input('custom_name') . '%');
        }

        // Filtrar por Correo
        if (request()->has('custom_email') && request()->filled('custom_email')) {
            $this->crud->addClause('where', 'email', 'like', '%' . request()->input('custom_email') . '%');
        }

        // Filtro para ver registros eliminados
        $this->crud->addFilter(
            [
                'type'  => 'simple',
                'name'  => 'trashed',
                'label' => 'Ver eliminados'
            ],
            false,
            function () {
                $this->crud->query = $this->crud->query->onlyTrashed();
            }
        );
    }


    protected function setupCreateOperation()
    {
        CRUD::setValidation(UserRequest::class);
        $user = backpack_user();
        $isSuper = ($user->role === 'superadmin' || $user->email === 'admin@ragon.com');

        // --- Bloque 1: Datos Personales ---
        CRUD::field('name')->label('Nombre Completo')->size(6);
        CRUD::field('email')->type('email')->label('Correo Electrónico')->size(6);

        CRUD::field('password')
            ->label('Contraseña')
            ->type('password')
            ->size(6);

        // --- Bloque de Roles (MySQL) ---
        $opcionesRoles = ['admin' => 'Administrador', 'proveedor' => 'Proveedor'];
        if ($isSuper) {
            $opcionesRoles['superadmin'] = 'Super Administrador';
        }

        CRUD::field('role')
            ->label('Rol (Base de Datos)')
            ->type('select_from_array')
            ->options($opcionesRoles)
            ->size(6);

        CRUD::field('rfc')->label('RFC')->size(6);
        CRUD::field('telefono')->label('Teléfono')->size(6);

        // --- Bloque 2: Sincronización Spatie ---
        CRUD::field('separator_security')
            ->type('custom_html')
            ->value('<br><h4>🛡️ Permisos y Seguridad</h4><hr>');

        CRUD::field('roles')
            ->label('Asignar Rol de Acceso (Spatie)')
            ->type('select_multiple') // Cambiado a select_multiple para mejor filtrado
            ->entity('roles')
            ->attribute('name')
            ->model('Spatie\Permission\Models\Role')
            ->pivot(true)
            ->options((function ($query) use ($isSuper) {
                // Si NO es superadmin, ocultamos el rol de superadmin de la lista de Spatie
                return $isSuper ? $query->get() : $query->where('name', '!=', 'superadmin')->get();
            }));
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();

        // Al editar, la contraseña es opcional
        $this->crud->modifyField('password', [
            'hint' => 'Déjalo vacío para mantener la contraseña actual.',
        ]);

        // Evitar que un admin normal acceda a editar a un superadmin mediante la URL (ID)
        $userActual = backpack_user();
        $targetUser = $this->crud->getCurrentEntry();
        if ($targetUser && $targetUser->role === 'superadmin' && $userActual->role !== 'superadmin' && $userActual->email !== 'admin@ragon.com') {
            abort(403, 'No tienes permisos para editar a un Super Administrador.');
        }
    }
}
