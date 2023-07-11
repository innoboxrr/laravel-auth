<?php

namespace Innoboxrr\LaravelAuth\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SecurePassword implements ValidationRule
{

    public function passes($attribute, $value)
    {

        // Verifica si la contraseña está presente y tiene al menos 8 caracteres
        if (empty($value) || strlen($value) < config('laravel-auth.password.length', 8)) {
            return false;
        }

        // Verifica si la contraseña contiene al menos una letra mayúscula
        if (!preg_match('/[A-Z]/', $value) && config('laravel-auth.password.uppercase')) {
            return false;
        }

        // Verifica si la contraseña contiene al menos un número
        if (!preg_match('/[0-9]/', $value) && config('laravel-auth.password.number')) {
            return false;
        }

        return true;
        
    }

    public function message()
    {

        return 'La :attribute debe tener al menos 8 caracteres y contener al menos una letra mayúscula y un número.';

    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        
        if (!$this->passes($attribute, $value)) {
        
            $fail($this->message());
        
        }

    }

}