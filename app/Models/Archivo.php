<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Archivo extends Model
{
    /**
     * Atributos que se pueden asignar de manera masiva.
     * Esto es una medida de seguridad (Mass Assignment) para evitar que 
     * se inserten campos no deseados en la base de datos.
     */
    protected $fillable = [
        'user_id',         // ID del usuario que subió el archivo
        'nombre_original', // Nombre real del archivo (ej: reporte.csv)
        'nombre_sistema',  // Nombre único generado para evitar colisiones (ej: 1705923_reporte.csv)
        'ruta'             // Dirección de almacenamiento en el disco
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
}