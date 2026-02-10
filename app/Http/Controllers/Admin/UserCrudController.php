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

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\User::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/user');
        CRUD::setEntityNameStrings('user', 'users');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        // Esto define qué columnas se ven en la tabla principal
        CRUD::column('name')->label('Nombre');
        CRUD::column('email')->label('Correo Electrónico');
        CRUD::column('role')->label('Rol');
        CRUD::column('id')->label('id');
        // No mostramos password por seguridad
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(\App\Http\Requests\UserRequest::class);

        // Esto define los inputs del formulario de crear/editar
        CRUD::field('name')->label('Nombre Completo');
        CRUD::field('email')->type('email')->label('Correo');
        
        // Campo para la contraseña (solo si se está creando o si se quiere cambiar)
        CRUD::field('password')->type('password')->label('Contraseña');
        
        // Tus campos personalizados basados en tu modelo User.php
        CRUD::field('id')->label('Código de Estudiante/Empleado');
        CRUD::field('rfc')->label('RFC');
        CRUD::field('telefono')->label('Teléfono');
        
        // Si 'role' es un texto simple en base de datos:
        CRUD::field('role')->type('select_from_array')->options([
            'admin' => 'Administrador',
            'user' => 'Usuario',
            'student' => 'Estudiante'
        ])->label('Rol Asignado');
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
