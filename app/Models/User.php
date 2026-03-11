<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes; // 1. Importar SoftDeletes
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;


class User extends Authenticatable
{
    // 2. Agregar SoftDeletes al listado de Traits utilizados
    use HasFactory, Notifiable, CrudTrait, HasRoles, LogsActivity, SoftDeletes;
    
    protected $guard_name = 'web';

    protected $fillable = [
        'name',
        'email',
        'password',
        'codigo',
        'rfc',
        'telefono',
        'role',
        'roles',
        'permissions',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
        ];
    }
    
    /**
     * 3. Configuración de las opciones del Log
     */
    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'codigo', 'rfc', 'telefono'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('user');
    }

    public function setPasswordAttribute($value)
    {
        if ($value) {
            $this->attributes['password'] = bcrypt($value); 
        }
    }

    public function archivos()
    {
        return $this->hasMany(Archivo::class);
    }
}