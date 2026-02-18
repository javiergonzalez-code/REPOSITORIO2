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

        // --- SEGURIDAD: Verificar Permisos ---
        if (!backpack_user()->can('list users')) {
            CRUD::denyAccess(['list', 'show', 'create', 'update', 'delete']);
        }

        if (!backpack_user()->can('create users')) {
            CRUD::denyAccess(['create']);
        }

        if (!backpack_user()->can('update users')) {
            CRUD::denyAccess(['update']);
        }

        if (!backpack_user()->can('delete users')) {
            CRUD::denyAccess(['delete']);
        }
    }

    protected function setupListOperation()
    {
        CRUD::column('name')->label('Nombre');
        CRUD::column('email')->label('Correo');
        CRUD::column('roles')->label('Roles')->type('relationship')->attribute('name');
        CRUD::column('created_at')->label('Creado')->type('date');
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(UserRequest::class);

        // --- Datos Básicos ---
        CRUD::field('name')->label('Nombre Completo')->size(6);
        CRUD::field('email')->type('email')->label('Correo Electrónico')->size(6);

        // --- Contraseña ---
        // Solo requerida en creación, opcional en edición
        CRUD::field('password')
            ->label('Contraseña')
            ->type('password')
            ->size(6)
            ->hint('Dejar vacío para mantener la actual (solo edición)');

        // --- Datos Extra ---
        CRUD::field('codigo')->label('Código')->size(6);
        CRUD::field('rfc')->label('RFC')->size(6);
        CRUD::field('telefono')->label('Teléfono')->size(6);

        // --- Roles y Permisos ---
        // Verifica el permiso que AHORA SÍ existe en el Seeder
        if (backpack_user()->can('manage roles') || backpack_user()->hasRole('admin')) {

            // Campo para asignar ROL (Spatie)
            CRUD::field('roles')
                ->type('relationship')
                ->label('Roles Asignados')
                ->name('roles') // la relación en el modelo User
                ->entity('roles')
                ->attribute('name')
                ->pivot(true)
                ->size(12); // Ocupa todo el ancho
        }
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
