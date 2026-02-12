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
        // only allow updates if the user is logged in
        return backpack_auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // Obtener el ID del usuario si se está editando (para ignorar su propio email en la validación unique)
        $userId = backpack_user()->id ?? null;
        // O mejor: $userId = $this->route('id');

        return [
            'name'     => 'required|min:2|max:255',
            'email'    => 'required|email|unique:users,email,' . $this->route('id'), // Ignorar el actual al editar
            'password' => 'sometimes|nullable|min:6', // 'required' solo en create, lógica compleja
            'codigo'   => 'nullable|string|max:20',
            'rfc'      => 'nullable|string|max:13',
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
            //
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
            //
        ];
    }
}
