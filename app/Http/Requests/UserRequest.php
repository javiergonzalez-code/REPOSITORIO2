<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Solo permitir si el usuario está autenticado en el panel administrativo
        return backpack_auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $userId = $this->route('id'); // Obtiene el ID del usuario en edición

        return [
            'name'     => 'required|min:2|max:255',
            'email'    => 'required|email|unique:users,email,' . $userId,
            // La contraseña es obligatoria solo si no hay un ID de usuario (es una creación)
            'password' => $userId ? 'nullable|min:6' : 'required|min:6',
            'codigo'   => 'nullable|string|max:20',
            'rfc'      => 'nullable|string|max:13',
            'telefono' => 'nullable|string|max:20', // Añadido para coincidir con tu modelo y controlador
        ];
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'name'     => 'Nombre',
            'email'    => 'Correo Electrónico',
            'password' => 'Contraseña',
            'codigo'   => 'Código',
            'rfc'      => 'RFC',
            'telefono' => 'Teléfono',
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'email.unique' => 'Este correo electrónico ya está en uso.',
            'password.required' => 'La contraseña es obligatoria para nuevos usuarios.',
        ];
    }
}