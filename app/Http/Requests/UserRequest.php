<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Elegant\Sanitizer\Laravel\SanitizesInput;
use App\Rules\ValidRFC;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    use SanitizesInput;

    public function authorize()
    {
        // Validación basada en la columna 'role' nativa
        return auth()->check() && in_array(auth()->user()->role, ['admin', 'superadmin']);
    }

    public function rules()
    {
        // El parámetro 'id' en la ruta contiene el CardCode (String)
        $userId = $this->route('id');
        
        return [
            'name'     => 'required|string|min:5|max:255',
            
            // 🚨 Mapeo: Validamos contra la columna E_Mail usando CardCode como identificador
            'email'    => [
                'required', 
                'email', 
                Rule::unique('users', 'E_Mail')->ignore($userId, 'CardCode')
            ],

            // 🚨 Mapeo: Validamos contra la columna LicTradNum (RFC)
            'rfc'      => [
                'nullable', 
                'string', 
                'min:12', 
                'max:13', 
                new ValidRFC(), 
                Rule::unique('users', 'LicTradNum')->ignore($userId, 'CardCode')
            ],

            'password' => $userId ? 'nullable|min:8|confirmed' : 'required|min:8|confirmed',
            'telefono' => 'nullable|string|max:20',
            'role'     => 'required|in:superadmin,admin,proveedor',
        ];
    }

    public function filters()
    {
        // Los filtros se aplican a los nombres de los inputs del formulario
        return [
            'name'     => 'trim|capitalize|empty_string_to_null',
            'email'    => 'trim|lowercase|empty_string_to_null',
            'rfc'      => 'trim|uppercase|empty_string_to_null',
            'telefono' => 'trim|digit|empty_string_to_null',
        ];
    }

    public function attributes()
    {
        return [
            'name'     => 'nombre',
            'email'    => 'correo electrónico',
            'password' => 'contraseña',
            'rfc'      => 'RFC',
            'telefono' => 'teléfono',
            'role'     => 'nivel de acceso',
        ];
    }

    public function messages()
    {
        return [
            'email.unique' => 'Este correo ya está registrado en el sistema SAP.',
            'rfc.unique'   => 'Este RFC ya pertenece a otro usuario.',
        ];
    }
}