<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\UserRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class UserCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
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

        // Solo permitir editar si tiene permiso
        if (!backpack_user()->can('edit users')) {
            $this->crud->denyAccess('update');
        }
    }

    protected function setupListOperation()
    {
        CRUD::column('name')->label('Nombre');
        CRUD::column('email')->label('Correo Electrónico');
        // Mostrar roles en la lista (opcional, solo visual)
        CRUD::column('roles')->type('relationship')->label('Roles')->attribute('name'); 
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(\App\Http\Requests\UserRequest::class);

        // --- CAMPOS GENERALES (Para todos) ---
        CRUD::field('name')->label('Nombre Completo');
        CRUD::field('email')->type('email')->label('Correo');
        CRUD::field('password')->type('password')->label('Contraseña')->hint('Dejar vacío para mantener la actual.');
        
        CRUD::field('codigo')->label('Código');
        CRUD::field('rfc')->label('RFC');
        CRUD::field('telefono')->label('Teléfono');

        // --- CAMPO PROTEGIDO (Solo para Super Admin) ---
        // Verificamos el rol AQUÍ para que sirva tanto al crear como al editar
        if (backpack_user()->hasRole('Super Admin')) { 
            CRUD::field('roles,permissions')
                ->type('checklist_dependency')
                ->label('Roles y Permisos')
                ->subfields([
                    'primary' => [
                        'label'            => 'Roles',
                        'name'             => 'roles', 
                        'entity'           => 'roles', 
                        'entity_secondary' => 'permissions', 
                        'attribute'        => 'name', 
                        'model'            => config('permission.models.role'), 
                        'pivot'            => true, 
                    ],
                    'secondary' => [
                        'label'          => 'Permisos',
                        'name'           => 'permissions', 
                        'entity'         => 'permissions', 
                        'entity_primary' => 'roles', 
                        'attribute'      => 'name', 
                        'model'          => config('permission.models.permission'), 
                        'pivot'          => true,
                    ],
                ]);
        }
    }

    protected function setupUpdateOperation()
    {
        // Al llamar a esto, se ejecuta la lógica de arriba, incluyendo la validación del rol 'Super Admin'
        $this->setupCreateOperation();
    }
}