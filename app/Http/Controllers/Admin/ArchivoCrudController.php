<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ArchivoCrudController
 * @package App\Http\Controllers\Admin
 */
class ArchivoCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\Archivo::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/archivo');
        CRUD::setEntityNameStrings('archivo', 'archivos');
    }

    protected function setupListOperation()
    {
        CRUD::column('nombre_original')->label('Nombre Original');
        CRUD::column('tipo_archivo')->label('Tipo');
        CRUD::column('modulo')->label('Módulo');
        CRUD::column('user')->type('relationship')->label('Usuario');
        CRUD::column('created_at')->label('Fecha de Subida');
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation([
            'nombre_original' => 'required|min:2',
            'modulo' => 'required',
        ]);

        CRUD::field('user_id')->type('select')->entity('user')->attribute('name')->label('Usuario');
        CRUD::field('nombre_original')->label('Nombre Original');
        CRUD::field('modulo')->label('Módulo');

        CRUD::field('ruta')->type('upload')->upload(true)->disk('public')->label('Archivo Adjunto');
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}