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
        'user_id', //Guarda el CardCode 
        'nombre_original',
        'nombre_sistema',
        'tipo_archivo',
        'modulo',
        'ruta'
    ];

    /**
     * Configuración del historial de cambios 
     */
    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        return LogOptions::defaults()
            // Define qué columnas queremos vigilar e historiar
            ->logOnly(['nombre_original', 'nombre_sistema', 'tipo_archivo', 'modulo', 'ruta'])
            
            // Solo guarda registro si el dato realmente cambió
            ->logOnlyDirty()
            
            // Si le dieron "Guardar" pero no le movieron a nada, no escribe nada en el log
            ->dontSubmitEmptyLogs()
            
            // Nombre del canal de logs en la BD
            ->useLogName('archivo')
            
            // Traduce los eventos nativos de Eloquent a español para la vista del Admin
            ->setDescriptionForEvent(fn(string $eventName) => match ($eventName) {
                'created' => 'Creación',
                'updated' => 'Actualización',
                'deleted' => 'Eliminación',
                default   => $eventName,
            });
    }

    /**
     * Relación con el modelo de Usuarios.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'CardCode');
    }

    /**
     * Te permite usar $archivo->ruta_url directamente en las vistas.
     */
    public function getRutaUrlAttribute()
    {
        return route('archivos.download', $this->id);
    }
}