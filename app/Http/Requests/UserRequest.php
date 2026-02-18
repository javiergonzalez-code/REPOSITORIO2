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
        // Esto obtiene el ID del usuario que estás editando desde la URL
        $userId = request()->route('id') ?? request()->route('user');

        return [
            'name' => 'required|min:5|max:255',
            // El email es único, EXCEPTO para el usuario que tiene este ID
            'email' => 'required|email|unique:users,email,' . $userId,
            // La contraseña es obligatoria al crear, pero opcional (nullable) al editar
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
