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

        // 1. Denegar operaciones de escritura para mantener la integridad de los logs
        CRUD::denyAccess(['create', 'update', 'delete']);

        // 2. Seguridad: Verificar permiso de lectura
        // Si el usuario NO tiene permiso 'list logs', le quitamos el acceso.
        if (!backpack_user()->can('list logs')) {
            CRUD::denyAccess(['list', 'show']);
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
