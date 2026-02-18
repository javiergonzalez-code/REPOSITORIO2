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
        
        // --- Mostrar Roles en la lista ---
        CRUD::column('roles')
            ->type('relationship_count')
            ->label('Roles')
            ->suffix(' rol(es)');
            
        CRUD::column('created_at')->label('Creado')->type('date');
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(UserRequest::class);

        // --- 1. Datos Básicos ---
        CRUD::field('name')->label('Nombre Completo')->size(6);
        CRUD::field('email')->type('email')->label('Correo Electrónico')->size(6);

        // --- 2. Contraseña ---
        CRUD::field('password')
            ->label('Contraseña')
            ->type('password')
            ->size(6)
            ->hint('Dejar vacío para mantener la actual (solo edición)');

        // --- 3. Datos Extra ---
        CRUD::field('codigo')->label('Código')->size(6);
        CRUD::field('rfc')->label('RFC')->size(6);
        CRUD::field('telefono')->label('Teléfono')->size(6);

        // --- 4. PODER DE SUPERUSUARIO: Asignar Roles y Permisos ---
        // Solo mostramos esto si el usuario actual es ADMIN o tiene permiso de gestionar roles
        if (backpack_user()->hasRole('admin') || backpack_user()->can('manage roles')) {
            
            // Separador visual
            CRUD::field('roles_and_permissions_separator')
                ->type('custom_html')
                ->value('<br><h4>🛡️ Asignación de Seguridad</h4><hr>');

            // A. Selector de ROLES (Checklist)
            CRUD::field('roles')
                ->label('Roles / Perfiles')
                ->type('checklist')
                ->entity('roles') // La relación en App\Models\User
                ->attribute('name') // Mostrar 'admin', 'proveedor', etc.
                ->model('Spatie\Permission\Models\Role')
                ->pivot(true);

            // B. Selector de PERMISOS EXTRAS (Checklist)
            // Permite dar permisos específicos sin asignar un rol completo
            CRUD::field('permissions')
                ->label('Permisos Directos (Excepciones)')
                ->type('checklist')
                ->entity('permissions')
                ->attribute('name')
                ->model('Spatie\Permission\Models\Permission')
                ->pivot(true);
        }
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}