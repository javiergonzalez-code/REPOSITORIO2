<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\UserRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Spatie\Permission\Traits\HasRoles;
use Backpack\CRUD\app\Models\Traits\CrudTrait;

class UserCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use CrudTrait, HasRoles;

    public function setup()
    {
        CRUD::setModel(\App\Models\User::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/user');
        CRUD::setEntityNameStrings('usuario', 'usuarios');

        // --- 1. SEGURIDAD: Verificar Permisos Globales ---
        // Si no tienes permiso de ver lista, fuera.

        // Si es admin, permitimos TODO sin preguntar.
        if (backpack_user()->hasRole('admin')) {
            CRUD::allowAccess(['list', 'show', 'create', 'update', 'delete']);
        } else {
            // Si NO es admin, entonces sí verificamos permisos uno por uno
            if (!backpack_user()->can('list users')) {
                CRUD::denyAccess(['list', 'show']);
            }


            // Restricciones por operación específica
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
    }
    protected function setupListOperation()
    {
        CRUD::column('name')->label('Nombre');
        CRUD::column('email')->label('Correo');

        // Columna especial para ver cuántos roles tiene
        CRUD::column('roles')
            ->type('relationship_count')
            ->label('Roles')
            ->suffix(' rol(es)');

        CRUD::column('created_at')->label('Creado')->type('date');
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(UserRequest::class);

        // --- Bloque 1: Datos Personales ---
        CRUD::field('name')->label('Nombre Completo')->size(6);
        CRUD::field('email')->type('email')->label('Correo Electrónico')->size(6);

        CRUD::field('password')
            ->label('Contraseña')
            ->type('password')
            ->size(6)
            // El hint solo tiene sentido al editar, pero no estorba al crear
            ->hint('Déjalo vacío para mantener la contraseña actual (solo al editar).');

        CRUD::field('codigo')->label('Código')->size(6);
        CRUD::field('rfc')->label('RFC')->size(6);
        CRUD::field('telefono')->label('Teléfono')->size(6);

        // --- Bloque 2: Seguridad (Roles y Permisos) ---
        // Solo mostramos esto si eres ADMIN o tienes permiso de gestionar roles
        if (backpack_user()->hasRole('admin') || backpack_user()->can('manage roles')) {

            CRUD::field('separator_security')
                ->type('custom_html')
                ->value('<br><h4>🛡️ Asignación de Seguridad</h4><hr>');

            // A. Selector de ROLES (Checklist)
            CRUD::field('roles')
                ->label('Roles / Perfiles')
                ->type('checklist')
                ->entity('roles')
                ->attribute('name')
                ->model('Spatie\Permission\Models\Role')
                ->pivot(true);

            // B. Selector de PERMISOS (Checklist - Excepciones)
            CRUD::field('permissions')
                ->label('Permisos Específicos (Adicionales)')
                ->type('checklist')
                ->entity('permissions')
                ->attribute('name')
                ->model('Spatie\Permission\Models\Permission')
                ->pivot(true);
        }
    }

    protected function setupUpdateOperation()
    {
        // Reutilizamos EXACTAMENTE la misma configuración de crear
        // Esto asegura que veas los checklists de roles/permisos al editar
        $this->setupCreateOperation();
    }
}
