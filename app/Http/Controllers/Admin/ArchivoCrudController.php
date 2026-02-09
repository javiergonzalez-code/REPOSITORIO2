<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ArchivoRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ArchivoCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ArchivoCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Archivo::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/archivo');
        CRUD::setEntityNameStrings('archivo', 'archivos');

        // 1. Restringir acceso total si no tiene el rol adecuado
        if (!backpack_user()->hasAnyRole(['Administrador', 'Proveedor'])) {
            CRUD::denyAccess(['list', 'create', 'update', 'delete']);
        }

        // // 2. Restringir solo la eliminación a los Admins
        // if (!backpack_user()->hasRole('Administrador')) {
        //     CRUD::denyAccess('delete');
        // }
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('nombre_original')->label('Archivo');
        CRUD::column('tipo_archivo')->label('Tipo');

        // Mostramos el nombre del usuario que lo subió usando la relación
        CRUD::addColumn([
            'label'     => "Subido por",
            'type'      => 'select',
            'name'      => 'user_id',
            'entity'    => 'user',
            'attribute' => 'name',
            'model'     => "App\Models\User",
        ]);
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(ArchivoRequest::class);

        // El ID del usuario se asigna automáticamente al que está logueado
        CRUD::addField([
            'type' => 'hidden',
            'name' => 'user_id',
            'default' => backpack_user()->id,
        ]);

        CRUD::field('nombre_original')->label('Nombre del documento');

        // Campo para subir el archivo físicamente
        CRUD::field('ruta')
            ->label('Archivo')
            ->type('upload')
            ->upload(true);

        // Estos campos podrías llenarlos automáticamente en el modelo o dejarlos aquí
        CRUD::field('tipo_archivo')->label('Extensión (PDF, Excel, etc)');
    }

    /**
     * Define what happens when the Update operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
