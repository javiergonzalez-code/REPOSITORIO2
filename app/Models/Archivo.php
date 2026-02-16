<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Archivo extends Model
{
    use HasFactory;
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
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

        // CORRECCIÓN PARA QUE EL SEEDER FUNCIONE:
        // Si el valor es un string (viene del Seeder o Factory), lo guardamos directo.
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
