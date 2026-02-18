<?php

namespace App\Models;

use Spatie\Activitylog\Models\Activity as SpatieActivity;
use Backpack\CRUD\app\Models\Traits\CrudTrait;

class Activity extends SpatieActivity
{
    use CrudTrait;

    // Esto permite que Backpack interactúe con la tabla activity_log
}