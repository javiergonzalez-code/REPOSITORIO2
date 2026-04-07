<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Archivo extends Model
{
    use HasFactory, SoftDeletes;
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
            // AÑADIMOS los campos correctos de la tabla archivos
            ->logOnly(['nombre_original', 'nombre_sistema', 'tipo_archivo', 'modulo', 'ruta', 'deleted_at'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('archivo')
            ->setDescriptionForEvent(fn(string $eventName) => match ($eventName) {
                'created' => 'Creación',
                'updated' => 'Actualización',
                'deleted' => 'Eliminación',
                'restored' => 'Restauración',
                default => $eventName,
            });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getRutaUrlAttribute()
    {
        return asset('storage/' . $this->ruta);
    }
    protected static function booted()
    {
        static::forceDeleted(function ($archivo) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($archivo->ruta);
        });
    }
}
