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

        // --- CAMPOS GENERALES ---
        CRUD::field('name')->label('Nombre Completo');
        CRUD::field('email')->type('email')->label('Correo');
        CRUD::field('password')->type('password')->label('Contraseña')->hint('Dejar vacío para mantener la actual.');
        CRUD::field('codigo')->label('Código');
        CRUD::field('rfc')->label('RFC');
        CRUD::field('telefono')->label('Teléfono');

        // --- GESTIÓN DE ROLES Y PERMISOS ---
        // Solo el Super Admin (o quien tenga permiso de editar roles) debería ver esto.
        // El 'checklist_dependency' maneja tanto Roles como Permisos visualmente.
        
        if (backpack_user()->hasRole('admin') || backpack_user()->can('edit users')) {
            CRUD::addField([
                'label'             => 'Roles y Permisos',
                'type'              => 'checklist_dependency',
                'name'              => 'roles,permissions', // Nombre compuesto requerido por este campo
                'subfields'         => [
                    'primary' => [
                        'label'            => 'Roles',
                        'name'             => 'roles', // Relación en User model
                        'entity'           => 'roles',
                        'entity_secondary' => 'permissions',
                        'attribute'        => 'name',
                        'model'            => config('permission.models.role'),
                        'pivot'            => true, 
                    ],
                    'secondary' => [
                        'label'          => 'Permisos Extras (Directos)',
                        'name'           => 'permissions', // Relación en User model
                        'entity'         => 'permissions',
                        'entity_primary' => 'roles',
                        'attribute'      => 'name',
                        'model'          => config('permission.models.permission'),
                        'pivot'          => true, 
                    ],
                ],
            ]);
        }
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
