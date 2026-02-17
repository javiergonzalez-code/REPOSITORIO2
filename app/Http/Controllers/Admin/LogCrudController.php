<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class LogCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\Log::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/log');
        CRUD::setEntityNameStrings('log', 'logs');

        $this->crud->allowAccess(['create', 'update', 'delete']);

        // Permiso: Solo ver si tiene permiso
        if (!backpack_user()->can('ver logs')) {
            CRUD::allowAccess(['list', 'show']);
        }
    }

    protected function setupListOperation()
    {
        CRUD::column('created_at')->label('Fecha');
        CRUD::column('user')->type('relationship')->attribute('name')->label('Usuario');
        CRUD::column('accion')->label('Acción');
        CRUD::column('modulo')->label('Módulo');
    }

    protected function setupShowOperation()
    {
        $this->setupListOperation();
    }
}