<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidRFC implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Expresión regular para validar RFC mexicano (Persona Física o Moral)
        // Acepta 3 o 4 letras (incluyendo Ñ y &), 6 dígitos de fecha (AAMMDD) y 3 caracteres alfanuméricos de homoclave
        $regex = '/^([A-ZÑ&]{3,4})(\d{2}(?:0[1-9]|1[0-2])(?:0[1-9]|[12]\d|3[01]))([A-Z\d]{3})$/i';

        if (!preg_match($regex, $value)) {
            $fail('El formato del :attribute no es un RFC válido.');
        }
    }
}