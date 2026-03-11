<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // 1. Importar SoftDeletes
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Archivo extends Model
{
    // 2. Agregar SoftDeletes a la clase
    use HasFactory, SoftDeletes;
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use LogsActivity;

    protected $fillable = [
        'user_id',
        'nombre_original',
        'nombre_sistema',
        'tipo_archivo',
        'modulo',
        'ruta'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['user_id', 'nombre_original', 'tipo_archivo', 'modulo', 'ruta'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('archivo');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function setRutaAttribute($value)
    {
        $attribute_name = "ruta";
        $disk = "private";
        $destination_path = "uploads/archivos";

        if (is_string($value) && $value != null) {
            $this->attributes[$attribute_name] = $value;
            return;
        }

        $this->uploadFileToDisk($value, $attribute_name, $disk, $destination_path);
    }

    public function getRutaUrlAttribute()
    {
        return asset('storage/' . $this->ruta);
    }
}