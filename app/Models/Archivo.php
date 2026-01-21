<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Archivo extends Model
{
    protected $fillable = ['user_id','nombre_original','nombre_sistema','ruta'];

    public function user() {
        return $this->belongsTo(User::class);

    }
}
