<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function authorize()
    {
        return backpack_auth()->check();
    }

    public function rules()
    {
        // Obtenemos el ID del usuario que se está editando (si existe)
        $userId = request()->route('id');

        return [
            'name'     => 'required|min:2|max:255',
            
            // IMPORTANTE: unique:users,email,EXCEPT_ID
            // Esto permite guardar tu propio perfil sin cambiar el correo
            'email'    => 'required|email|unique:users,email,'.$userId,
            
            // Contraseña: Obligatoria solo al CREAR ($userId es null), opcional al editar
            'password' => $userId ? 'nullable|min:6' : 'required|min:6',
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'nombre',
            'email' => 'correo electrónico',
            'password' => 'contraseña',
        ];
    }

    public function messages()
    {
        return [
            'email.unique' => 'Este correo ya está registrado por otro usuario.',
        ];
    }
}