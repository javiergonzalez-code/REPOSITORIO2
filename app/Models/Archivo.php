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
        'ruta',
        'modulo'
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
        $attribute_name = "ruta"; // El nombre de la columna en la BD
        $disk = "public"; // Guardar en storage/app/public
        $destination_path = "uploads/archivos"; // Carpeta destino

        // Lógica estándar de Backpack para subida de archivos
        $this->uploadFileToDisk($value, $attribute_name, $disk, $destination_path);
    }

    // Opcional: Accessor para obtener la URL completa
    public function getRutaUrlAttribute()
    {
        return asset('storage/' . $this->ruta);
    }
}
