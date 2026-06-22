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
     */
    protected $fillable = [
        'user_id', 
        'accion',
        'modulo' 
    ];

    /**
     * Define la relación inversa de pertenencia
     * Se especifica 'user_id' como llave foránea y 'CardCode' como llave local
     * * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'CardCode');
    }
}