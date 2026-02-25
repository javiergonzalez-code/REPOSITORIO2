<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
// 1. Importamos el Trait del paquete
use Elegant\Sanitizer\Laravel\SanitizesInput;
// Importamos nuestra nueva regla personalizada
use App\Rules\ValidRFC;

class UserRequest extends FormRequest
{
    // 2. Usamos el Trait para habilitar la sanitización automática antes de validar
    use SanitizesInput;

    public function authorize()
    {
        return backpack_auth()->check();
    }

    public function rules()
    {
        // Obtiene el ID del usuario desde la ruta para la excepción de email único
        $userId = request()->route('id') ?? request()->route('user');

        return [
            'name'     => 'required|min:5|max:255',
            'email'    => 'required|email|unique:users,email,' . $userId,
            'password' => $userId ? 'nullable|min:6' : 'required|min:6',
            
            // Reglas para los nuevos campos detectados en UserCrudController:
            'codigo'   => 'nullable|string|max:50', 
            // Implementamos la regla ValidRFC aquí pasándola en un array
            'rfc'      => ['nullable', 'string', 'min:12', 'max:13', new ValidRFC()], 
            'telefono' => 'nullable|string|max:20', 
        ];
    }

    /**
     * 3. Definimos los filtros de Sanitización.
     * Estos se ejecutan ANTES de las reglas de validación (rules).
     */
    public function filters()
    {
        return [
            // 'trim': Quita espacios al inicio y final.
            // 'capitalize': Pone la primera letra de cada palabra en mayúscula (Juan Perez).
            // 'empty_string_to_null': Si el campo viene vacío "", lo convierte en NULL.
            'name' => 'trim|capitalize|empty_string_to_null',

            // 'lowercase': Convierte todo a minúsculas (crucial para emails).
            'email' => 'trim|lowercase|empty_string_to_null',

            // Para RFC y Código, suele ser mejor todo en mayúsculas y sin espacios extra.
            'rfc'    => 'trim|uppercase|empty_string_to_null',
            'codigo' => 'trim|uppercase|empty_string_to_null',

            // 'digit': Elimina todo lo que NO sea número (quita guiones, paréntesis, espacios).
            // Ejemplo: "(555) 123-456" -> "555123456"
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