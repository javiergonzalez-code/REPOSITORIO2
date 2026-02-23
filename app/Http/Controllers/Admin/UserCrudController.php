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

    public function setup()
    {
        CRUD::setModel(\App\Models\User::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/user');
        CRUD::setEntityNameStrings('usuario', 'usuarios');

        $user = backpack_user();

        // Seguridad basada en tu columna 'role'
        if ($user->role !== 'admin' && $user->email !== 'admin@ragon.com') {
            CRUD::denyAccess(['list', 'show', 'create', 'update', 'delete']);
        }
    }

    protected function setupListOperation()
    {
        // En la lista sí puedes dejar el ID porque es solo una columna visual
        CRUD::column('id')->label('ID');
        CRUD::column('name')->label('Nombre');
        CRUD::column('email')->label('Correo');
        CRUD::column('role')->label('Rol Base'); // Agregamos visualización de tu columna

        CRUD::column('roles')
            ->type('relationship_count')
            ->label('Roles Spatie')
            ->suffix(' rol(es)');

        CRUD::column('created_at')->label('Creado')->type('date');
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(UserRequest::class);

        // --- Bloque 1: Datos Personales ---
        
        // CORRECCIÓN: Se eliminó el field('id'), Backpack lo maneja solo.

        CRUD::field('name')->label('Nombre Completo')->size(6);
        CRUD::field('email')->type('email')->label('Correo Electrónico')->size(6);

        CRUD::field('password')
            ->label('Contraseña')
            ->type('password')
            ->size(6)
            ->hint('Déjalo vacío para mantener la contraseña actual (solo al editar).');

        // IMPORTANTE: Campo para editar tu columna física 'role'
        CRUD::field('role')
            ->label('Rol (Columna Base de Datos)')
            ->type('select_from_array')
            ->options(['admin' => 'Administrador', 'proveedor' => 'Proveedor'])
            ->size(6);

        CRUD::field('rfc')->label('RFC')->size(6);
        CRUD::field('telefono')->label('Teléfono')->size(6);

        // --- Bloque 2: Seguridad (Spatie) ---
        // Usamos la lógica de tu columna física para dar permiso de asignar roles de Spatie
        if (backpack_user()->role === 'admin' || backpack_user()->email === 'admin@ragon.com') {

            CRUD::field('separator_security')
                ->type('custom_html')
                ->value('<br><h4>🛡️ Sincronización con Spatie</h4><hr>');

            CRUD::field('roles')
                ->label('Roles Spatie (Tablas ocultas)')
                ->type('checklist')
                ->entity('roles')
                ->attribute('name')
                ->model('Spatie\Permission\Models\Role')
                ->pivot(true);
        }
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}