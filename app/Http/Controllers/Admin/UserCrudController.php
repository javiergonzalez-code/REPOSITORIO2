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
        CRUD::setFromDb(); // set columns from db columns.

        /**
         * Columns can be defined using the fluent syntax:
         * - CRUD::column('price')->type('number');
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
protected function setupCreateOperation()
{
    CRUD::setValidation(UserRequest::class);

    CRUD::field('name')->label('Nombre');
    CRUD::field('email')->type('email');
    CRUD::field('password')->type('password');

    // CAMPO PARA ROLES
    CRUD::addField([
        'label'      => "Roles",
        'type'       => 'select_multiple',
        'name'       => 'roles', 
        'entity'     => 'roles',
        'attribute'  => 'name',
        'model'      => "Spatie\Permission\Models\Role",
        'pivot'      => true, // Importante para relaciones N:N
    ]);

    // CAMPO PARA PERMISOS (Opcional)
    CRUD::addField([
        'label'      => "Permisos Directos",
        'type'       => 'select_multiple',
        'name'       => 'permissions',
        'entity'     => 'permissions',
        'attribute'  => 'name',
        'model'      => "Spatie\Permission\Models\Permission",
        'pivot'      => true,
    ]);
}

protected function setupUpdateOperation()
{
    $this->setupCreateOperation(); // Esto copia lo de la función create para ahorrar código

    // Manejo especial para la contraseña en edición
    CRUD::field('password')
        ->type('password')
        ->hint('Deja en blanco para no cambiar la contraseña');
}
}