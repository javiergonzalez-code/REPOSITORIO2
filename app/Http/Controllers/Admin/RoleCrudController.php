<?php

namespace App\Http\Controllers\Admin;

use Backpack\PermissionManager\app\Http\Controllers\RoleCrudController as OriginalRoleCrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class RoleCrudController extends OriginalRoleCrudController
{
    public function setup()
    {
        parent::setup();
        // Aquí podrías personalizar cosas en el futuro si quisieras
    }
}