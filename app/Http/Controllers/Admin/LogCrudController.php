<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use App\Models\Activity;

/**
 * Clase LogCrudController
 * Se encarga de mostrar el historial de auditoría (quién hizo qué y cuándo).
 */
class LogCrudController extends CrudController
{
    // Solo se utilizan List (ver tabla) y Show (ver detalle), ya que los logs no deben editarse ni borrarse.
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configuración inicial del CRUD de Auditoría.
     */
    public function setup()
    {
        // Define el modelo de Actividad (normalmente de la librería spatie/laravel-activitylog)
        CRUD::setModel(Activity::class);
        
        // Define la URL de acceso: /admin/log
        CRUD::setRoute(config('backpack.base.route_prefix') . '/log');
        
        // Nombres para la interfaz en singular y plural
        CRUD::setEntityNameStrings('auditoría', 'auditorías');

        // SEGURIDAD: Desactiva por completo la posibilidad de crear, editar o eliminar logs manualmente.
        CRUD::denyAccess(['create', 'update', 'delete']);

        // CONTROL DE ACCESO: Verifica si el usuario autenticado tiene el permiso específico para ver logs.
        if (!backpack_user()->can('list logs')) {
            CRUD::denyAccess(['list', 'show']);
        }
    }

    /**
     * Configuración de la tabla principal de auditoría.
     */
    protected function setupListOperation()
    {
        // Muestra la fecha y hora exacta del movimiento realizado.
        CRUD::column('created_at')->type('datetime')->label('Fecha');

        /**
         * Columna del Usuario (Causer):
         * Identifica a la persona que realizó la acción.
         * Se usa una relación para traer el 'name' desde el modelo User.
         */
        CRUD::column('causer_id')
            ->label('Usuario')
            ->type('select')
            ->entity('causer') // Relación definida en el modelo Activity
            ->attribute('name')
            ->model('App\Models\User');

        // Descripción de la acción (ej: "created", "updated", "deleted").
        CRUD::column('description')->label('Evento');

        // Tipo de modelo que fue afectado (ej: App\Models\User, App\Models\Archivo).
        CRUD::column('subject_type')->label('Módulo');
        
        // El ID único del registro que fue modificado o eliminado.
        CRUD::column('subject_id')->label('ID Ref');
    }

    /**
     * Configuración de la vista detallada de un log.
     */
    protected function setupShowOperation()
    {
        // Reutiliza las columnas configuradas en la lista (ListOperation).
        $this->setupListOperation();

        /**
         * Columna de Propiedades (JSON):
         * Muestra los valores antiguos y los nuevos tras un cambio.
         * Backpack renderiza este JSON de forma legible en la vista 'Show'.
         */
        CRUD::column('properties')
            ->type('json')
            ->label('Detalles del Cambio');
    }
}