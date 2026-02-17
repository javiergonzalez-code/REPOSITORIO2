<?php

namespace App\Http\Controllers\Admin;

use Backpack\PermissionManager\app\Http\Controllers\PermissionCrudController as OriginalPermissionCrudController;

class PermissionCrudController extends OriginalPermissionCrudController
{
    public function setup()
    {
        parent::setup();
        // Acceso restringido solo a admins o quienes tengan permiso 'manage permissions'
        if (!backpack_user()->hasRole('admin') && !backpack_user()->can('manage permissions')) {
            $this->crud->denyAccess(['list', 'create', 'update', 'delete']);
        }
    }
}