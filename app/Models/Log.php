<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Log extends Model
{
    use HasFactory;
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    /**
     * Define la "lista blanca" de campos que pueden ser llenados masivamente.
     * Esto permite usar Log::create([...]) de forma segura.
     */
    protected $fillable = [
        'user_id', // ID del usuario que realizó la acción
        'accion',  // Descripción de lo que pasó (ej: "Subió un archivo")
        'modulo'  // Área del sistema donde ocurrió (ej: "INPUTS", "AUTH")

    ];

    /**
     * Define la relación inversa de pertenencia.
     * Indica que cada registro de log pertenece a un usuario específico.
     * * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        // Esto permite obtener los datos del usuario desde el log: $log->user->name
        return $this->belongsTo(User::class);
    }
}