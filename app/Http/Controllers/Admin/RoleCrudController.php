<?php

namespace App\Http\Controllers\Admin;

use Backpack\PermissionManager\app\Http\Controllers\RoleCrudController as OriginalRoleCrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class RoleCrudController extends OriginalRoleCrudController
{
    public function setup()
    {
        parent::setup();

        // Si NO es admin Y NO tiene permiso de gestionar roles, fuera.
        if (!backpack_user()->hasRole('admin') && !backpack_user()->can('manage roles')) {
            CRUD::denyAccess(['list', 'create', 'update', 'delete']);
        }
    }
}
