<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use App\Models\Activity;

class LogCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

public function setup()
    {
        // 2. CAMBIO AQUÍ: Cambiamos \App\Models\Log::class por Activity::class
        CRUD::setModel(Activity::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/log');
        CRUD::setEntityNameStrings('auditoría', 'auditorías');

        CRUD::denyAccess(['create', 'update', 'delete']);

        if (!backpack_user()->can('list logs')) {
            CRUD::denyAccess(['list', 'show']);
        }
    }

protected function setupListOperation()
    {
        // Fecha del movimiento
        CRUD::column('created_at')->type('datetime')->label('Fecha');

        // Quién lo hizo (el CAUSER)
        CRUD::column('causer_id')
            ->label('Usuario')
            ->type('select')
            ->entity('causer')
            ->attribute('name')
            ->model('App\Models\User');

        // Qué hizo (created, updated, deleted)
        CRUD::column('description')->label('Evento');

        // Sobre qué registro (User, etc.)
        CRUD::column('subject_type')->label('Módulo');
        
        // ID del registro afectado
        CRUD::column('subject_id')->label('ID Ref');
    }

    protected function setupShowOperation()
    {
        $this->setupListOperation();

        // Añadimos una columna especial para ver qué datos cambiaron (en formato JSON)
        CRUD::column('properties')
            ->type('json')
            ->label('Detalles del Cambio');
    }
}