<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Log extends Model
{
    // Esta línea autoriza a Laravel a escribir en estas columnas
    protected $fillable = [
        'user_id', 
        'accion', 
        'modulo', 
        'ip'
    ];

    /**
     * Relación con el usuario
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}