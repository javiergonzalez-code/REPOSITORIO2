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
        CRUD::column('created_at')->type('datetime')->label('Fecha');

        CRUD::column('causer_id')
            ->label('Usuario')
            ->type('select')
            ->entity('causer')
            ->attribute('name')
            ->model('App\Models\User');

        // BÚSQUEDA CORREGIDA PARA EVENTOS
        CRUD::column('description')
            ->label('Evento')
            ->type('closure')
            ->function(function($entry) {
                $map = [
                    'created' => 'Creación / Carga',
                    'updated' => 'Actualización',
                    'deleted' => 'Eliminación',
                    'restored' => 'Restauración',
                ];
                return $map[strtolower($entry->description)] ?? ucfirst($entry->description);
            })
            ->searchLogic(function ($query, $column, $searchTerm) {
                // El Closure interno ($q) evita que este "OR" rompa el filtro de Usuario
                $query->orWhere(function($q) use ($searchTerm) {
                    $term = strtolower($searchTerm);
                    
                    $q->where('description', 'like', '%'.$term.'%');

                    if (str_contains('carga creación creacion', $term)) {
                        $q->orWhereIn('description', ['created', 'Creación']);
                    }
                    if (str_contains('actualización actualizacion', $term)) {
                        $q->orWhereIn('description', ['updated', 'Actualización']);
                    }
                    if (str_contains('eliminación eliminacion borrado', $term)) {
                        $q->orWhereIn('description', ['deleted', 'Eliminación']);
                    }
                });
            });

        // BÚSQUEDA CORREGIDA PARA MÓDULOS
        CRUD::column('subject_type')
            ->label('Módulo')
            ->type('closure')
            ->function(function($entry) {
                if (!$entry->subject_type) return '-';
                $parts = explode('\\', $entry->subject_type);
                return end($parts);
            })
            ->searchLogic(function ($query, $column, $searchTerm) {
                // Closure interno de protección
                $query->orWhere(function($q) use ($searchTerm) {
                    $q->where('subject_type', 'like', '%'.$searchTerm.'%');
                });
            });
            
        CRUD::column('subject_id')->label('ID Ref');

        // ==========================================
        // FILTROS LATERALES (Ya no chocarán con la búsqueda)
        // ==========================================
        
        $this->crud->addFilter([
            'name'  => 'causer_id',
            'type'  => 'select2',
            'label' => 'Filtro por Usuario'
        ], function () {
            return \App\Models\User::all()->pluck('name', 'id')->toArray();
        }, function ($value) {
            $this->crud->addClause('where', 'causer_id', $value);
        });

        $this->crud->addFilter([
            'name'  => 'description',
            'type'  => 'dropdown',
            'label' => 'Filtro por Acción'
        ], [
            'created' => 'Carga / Creación',
            'updated' => 'Actualización',
            'deleted' => 'Eliminación',
        ], function ($value) {
            // Soporte para registros viejos (inglés) y nuevos (español)
            $terminos = [$value, ucfirst($value)];
            if ($value == 'created') array_push($terminos, 'Creación', 'Carga');
            if ($value == 'updated') array_push($terminos, 'Actualización');
            if ($value == 'deleted') array_push($terminos, 'Eliminación');
            
            $this->crud->addClause('whereIn', 'description', $terminos);
        });

        $this->crud->addFilter([
            'name'  => 'subject_type',
            'type'  => 'select2',
            'label' => 'Filtro por Módulo'
        ], function () {
            $types = \App\Models\Activity::select('subject_type')->whereNotNull('subject_type')->distinct()->get();
            $options = [];
            foreach($types as $type) {
                $parts = explode('\\', $type->subject_type);
                $options[$type->subject_type] = end($parts);
            }
            return $options;
        }, function ($value) {
            $this->crud->addClause('where', 'subject_type', $value);
        });
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