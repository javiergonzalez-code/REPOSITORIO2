<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Archivo extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $fillable = [
        'user_id', // 🚨 Ahora guardará el CardCode (String) en lugar del id numérico
        'nombre_original',
        'nombre_sistema',
        'tipo_archivo',
        'modulo',
        'ruta'
    ];

    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nombre_original', 'nombre_sistema', 'tipo_archivo', 'modulo', 'ruta'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('archivo')
            ->setDescriptionForEvent(fn(string $eventName) => match ($eventName) {
                'created' => 'Creación',
                'updated' => 'Actualización',
                'deleted' => 'Eliminación',
                default => $eventName,
            });
    }

    public function user()
    {
        // 🚨 MUY IMPORTANTE: Hacemos la relación explícita para SQL Server
        // Le indicamos que el 'user_id' de esta tabla se conecta con el 'CardCode' de la tabla User
        return $this->belongsTo(User::class, 'user_id', 'CardCode');
    }

    public function getRutaUrlAttribute()
    {
        // El ID del archivo sí sigue siendo numérico y autoincrementable, por lo que esto se queda igual
        return route('archivos.download', $this->id);
    }
}