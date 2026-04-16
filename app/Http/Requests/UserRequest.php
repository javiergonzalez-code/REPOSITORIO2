<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Elegant\Sanitizer\Laravel\SanitizesInput;
use App\Rules\ValidRFC;

class UserRequest extends FormRequest
{
    use SanitizesInput;

    public function authorize()
    {
        // 🚨 CORRECCIÓN: Usar auth nativo y validar la columna role
        return auth()->check() && in_array(auth()->user()->role, ['admin', 'superadmin']);
    }

    public function rules()
    {
        // 🚨 Leemos el parámetro 'id' (que ahora es el CardCode) en lugar de 'user'
        $userId = request()->route('id');
        
        return [
            'name'     => 'required|min:5|max:255',
            // 🚨 Aseguramos buscar por CardCode en el Unique
            'email'    => 'required|email|unique:users,email,' . $userId . ',CardCode',
            'password' => $userId ? 'nullable|min:6' : 'required|min:6',

            'codigo'   => 'nullable|string|max:50',
            'rfc'      => ['nullable', 'string', 'min:12', 'max:13', new ValidRFC()],
            'telefono' => 'nullable|string|max:20',
        ];
    }

    public function filters()
    {
        return [
            'name' => 'trim|capitalize|empty_string_to_null',
            'email' => 'trim|lowercase|empty_string_to_null',
            'rfc'    => 'trim|uppercase|empty_string_to_null',
            'codigo' => 'trim|uppercase|empty_string_to_null',
            'telefono' => 'trim|digit|empty_string_to_null',
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'nombre',
            'email' => 'correo electrónico',
            'password' => 'contraseña',
            'rfc' => 'RFC',
            'telefono' => 'teléfono',
            'codigo' => 'código',
        ];
    }

    public function messages()
    {
        return [
            'email.unique' => 'Este correo ya está registrado por otro usuario.',
        ];
    }
}