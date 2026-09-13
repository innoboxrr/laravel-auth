<?php

namespace Innoboxrr\LaravelAuth\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * La contraseña que exige la aplicación: longitud mínima y, si se configura,
 * una mayúscula y un número.
 *
 * Las reglas vivían en `laravel-auth.routes.password` y esta clase las buscaba
 * en `laravel-auth.password`, así que nunca se aplicaba nada más que los 8
 * caracteres por omisión. Y el mensaje decía siempre "8 caracteres, una
 * mayúscula y un número", en español, se exigiera o no.
 */
class SecurePassword implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $rules = self::rules();
        $value = is_string($value) ? $value : '';

        if (mb_strlen($value) < $rules['length']) {
            $fail(__('The :attribute must be at least :length characters.', ['length' => $rules['length']]));

            return;
        }

        if ($rules['uppercase'] && ! preg_match('/[A-Z]/', $value)) {
            $fail(__('The :attribute must contain at least one uppercase letter.'));

            return;
        }

        if ($rules['number'] && ! preg_match('/[0-9]/', $value)) {
            $fail(__('The :attribute must contain at least one number.'));
        }
    }

    /**
     * Se conserva para quien la llamaba directamente.
     */
    public function passes($attribute, $value): bool
    {
        $passes = true;

        $this->validate((string) $attribute, $value, function () use (&$passes): void {
            $passes = false;
        });

        return $passes;
    }

    /**
     * Las reglas vigentes: las de `password`, y si no, las de
     * `routes.password`, donde estaban antes.
     *
     * @return array{length: int, uppercase: bool, number: bool}
     */
    public static function rules(): array
    {
        $current = config('laravel-auth.password') ?: [];
        $legacy = config('laravel-auth.routes.password') ?: [];

        return [
            'length' => (int) ($current['length'] ?? $legacy['length'] ?? 8),
            'uppercase' => (bool) ($current['uppercase'] ?? $legacy['uppercase'] ?? false),
            'number' => (bool) ($current['number'] ?? $legacy['number'] ?? false),
        ];
    }
}
