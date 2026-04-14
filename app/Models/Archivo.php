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
        'user_id',
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
        return $this->belongsTo(User::class);
    }

    public function getRutaUrlAttribute()
    {
        return route('archivos.download', $this->id);
    }
}