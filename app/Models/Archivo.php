<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// 1. Importamos las clases necesarias de Spatie Activitylog
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Archivo extends Model
{
    use HasFactory;
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    // 2. Usamos el Trait para habilitar el registro de actividades
    use LogsActivity;

    /**
     * Atributos que se pueden asignar de manera masiva.
     * Esto es una medida de seguridad (Mass Assignment) para evitar que 
     * se inserten campos no deseados en la base de datos.
     */
    protected $fillable = [
        'user_id',
        'nombre_original',
        'nombre_sistema',
        'tipo_archivo',
        'modulo',
        'ruta'
    ];

    /**
     * 3. Configuración de las opciones del Log para el modelo Archivo
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            // Definimos qué campos queremos auditar si cambian
            ->logOnly(['user_id', 'nombre_original', 'tipo_archivo', 'modulo', 'ruta'])
            // Evitamos que se registre el log si no hubo cambios reales
            ->logOnlyDirty()
            // No guardamos logs vacíos
            ->dontSubmitEmptyLogs()
            // Le damos un nombre específico a esta bitácora para filtrarla fácilmente
            ->useLogName('archivo');
    }

    /**
     * Define la relación "pertenece a" (Muchos a Uno).
     * Indica que cada registro en la tabla 'archivos' pertenece a un 'User'.
     */
    public function user()
    {
        // Esto permite acceder al nombre del autor así: $archivo->user->name
        return $this->belongsTo(User::class);
    }

    public function setRutaAttribute($value)
    {
        $attribute_name = "ruta";
        $disk = "public";
        $destination_path = "uploads/archivos";


        if (is_string($value) && $value != null) {
            $this->attributes[$attribute_name] = $value;
            return;
        }

        // Si no es string (es un archivo subido desde el formulario), dejamos que Backpack lo procese
        $this->uploadFileToDisk($value, $attribute_name, $disk, $destination_path);
    }

    // Opcional: Accessor para obtener la URL completa
    public function getRutaUrlAttribute()
    {
        return asset('storage/' . $this->ruta);
    }
}