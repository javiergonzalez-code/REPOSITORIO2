<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Log extends Model
{
    use HasFactory;

    /**
     * Define la "lista blanca" de campos que pueden ser llenados masivamente.
     * El campo 'user_id' ahora almacenará el CardCode (String).
     */
    protected $fillable = [
        'user_id', // ID del usuario que realizó la acción (CardCode)
        'accion',  // Descripción de lo que pasó (ej: "Subió un archivo")
        'modulo'   // Área del sistema donde ocurrió (ej: "INPUTS", "AUTH")
    ];

    /**
     * Define la relación inversa de pertenencia.
     * REFACTORIZACIÓN PARA SQL SERVER: 
     * Se especifica 'user_id' como llave foránea y 'CardCode' como llave local.
     * * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        // Esto permite obtener los datos del usuario desde el log: $log->user->CardName
        return $this->belongsTo(User::class, 'user_id', 'CardCode');
    }
}