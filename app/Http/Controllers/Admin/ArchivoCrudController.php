<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Clase ArchivoCrudController
 * Gestiona el ciclo de vida (CRUD) de los archivos subidos al sistema.
 * @package App\Http\Controllers\Admin
 */
class ArchivoCrudController extends CrudController
{
    // Habilita las operaciones básicas de Backpack: Listar, Crear, Editar, Eliminar y Mostrar.
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    /**
     * Configuración general del panel CRUD.
     * Se ejecuta para todas las operaciones.
     */
    public function setup()
    {
        $this->crud->setModel(\App\Models\Archivo::class);
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/archivo');
        $this->crud->setEntityNameStrings('archivo', 'archivos');

        // --- LÓGICA DE SEGURIDAD Y PERMISOS ---

        // 1. Control de visualización: Si el usuario no tiene permiso, se deniega ver la lista y el detalle.
        if (!backpack_user()->can('list archivos')) {
            CRUD::denyAccess(['list', 'show']);
        }

        // 2. Control de subida: Si no tiene permiso de carga, se desactiva el botón de creación.
        if (!backpack_user()->can('upload archivos')) {
            CRUD::denyAccess(['create']);
        }

        // 3. Control de borrado: Restringe la eliminación física de los registros.
        if (!backpack_user()->can('delete archivos')) {
            CRUD::denyAccess(['delete']);
        }

        // Se bloquea globalmente la operación de actualizar (Editar) para este controlador.
        // Útil si los archivos son inmutables una vez subidos.
        CRUD::denyAccess(['update']);
    }

    /**
     * Configuración de la vista de lista (Tabla).
     */
    protected function setupListOperation()
    {
        // Define las columnas que se mostrarán en la tabla de resultados
        CRUD::column('nombre_original')->label('Nombre Original'); // Nombre tal cual se subió el archivo
        CRUD::column('tipo_archivo')->label('Tipo');              // Extensión o MIME type
        CRUD::column('modulo')->label('Módulo');                  // Área del sistema a la que pertenece

        // Columna de relación: Muestra el nombre del usuario dueño del archivo
        CRUD::column('user')->type('relationship')->label('Usuario');

        CRUD::column('created_at')->label('Fecha de Subida');     // Timestamp de creación

        // Filtro para ver registros eliminados
        $this->crud->addFilter(
            [
                'type'  => 'simple',
                'name'  => 'trashed',
                'label' => 'Ver eliminados'
            ],
            false,
            function () {
                $this->crud->query = $this->crud->query->onlyTrashed();
            }
        );
    }

    /**
     * Configuración de la vista de creación (Formulario).
     */
    protected function setupCreateOperation()
    {
        // Define las reglas de validación para el backend
        CRUD::setValidation([
            'nombre_original' => 'required|min:2',
            'modulo' => 'required',
        ]);

        // Campo para seleccionar el usuario relacionado
        CRUD::field('user_id')
            ->type('select')
            ->entity('user')
            ->attribute('name')
            ->label('Usuario');

        CRUD::field('nombre_original')->label('Nombre Original');
        CRUD::field('modulo')->label('Módulo');

        // Configuración del campo de carga de archivo
        CRUD::field('ruta')
            ->type('upload')
            ->upload(true)      // Indica que es un campo de archivo real
            ->disk('local')     // Define el disco de almacenamiento (config/filesystems.php)
            ->label('Archivo Adjunto');
    }

    /**
     * Configuración de la vista de edición.
     * Reutiliza los campos de la operación de creación.
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
