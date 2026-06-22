<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Elegant\Sanitizer\Laravel\SanitizesInput; // Paquete para limpiar datos antes de validar
use App\Rules\ValidRFC;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    use SanitizesInput;

    public function authorize()
    {
        // Solo Admins o Superadmins pueden usar este request (crear/editar)
        return auth()->check() && in_array(auth()->user()->role, ['admin', 'superadmin']);
    }

    public function rules()
    {
        // El parámetro '{id}' de la ruta es un String y trae el CardCode (de SAP)
        $userId = $this->route('id');
        
        return [
            'name'     => 'required|string|min:5|max:255',
            
            // Valida contra la columna real 'E_Mail'. Si es edición, ignora su propio CardCode
            'email'    => [
                'required', 
                'email', 
                Rule::unique('users', 'E_Mail')->ignore($userId, 'CardCode')
            ],

            // Valida contra la columna real 'LicTradNum' (RFC). Aplica regla personalizada ValidRFC
            'rfc'      => [
                'nullable', 
                'string', 
                'min:12', 
                'max:13', 
                new ValidRFC(), 
                Rule::unique('users', 'LicTradNum')->ignore($userId, 'CardCode')
            ],

            // Si hay CardCode en la ruta (Edit) la contraseña es opcional. Si es vacío (Create) es obligatoria
            'password' => $userId ? 'nullable|min:8|confirmed' : 'required|min:8|confirmed',
            'telefono' => 'nullable|string|max:20',
            'role'     => 'required|in:superadmin,admin,proveedor',
        ];
    }

    public function filters()
    {
        // Sanitización al vuelo: limpia los inputs del formulario ANTES de validarlos
        return [
            'name'     => 'trim|capitalize|empty_string_to_null',
            'email'    => 'trim|lowercase|empty_string_to_null',
            'rfc'      => 'trim|uppercase|empty_string_to_null', // Forza mayúsculas para el RFC de SAP
            'telefono' => 'trim|digit|empty_string_to_null', // 
        ];
    }

    public function attributes()
    {
        // Traduce los nombres de los inputs en los mensajes de error
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
        // Mensajes personalizados para los casos únicos
        return [
            'email.unique' => 'Este correo ya está registrado en el sistema SAP.',
            'rfc.unique'   => 'Este RFC ya pertenece a otro usuario.',
        ];
    }
}