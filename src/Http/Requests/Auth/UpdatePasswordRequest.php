<?php

namespace Innoboxrr\LaravelAuth\Http\Requests\Auth;

use Closure;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Innoboxrr\LaravelAuth\Rules\SecurePassword;

class UpdatePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * La contraseña actual se comprueba como una regla más. Antes era un 422
     * con `{"error": "..."}`, fuera del formato de errores de validación, así
     * que el formulario no sabía en qué campo pintarlo.
     */
    public function rules(): array
    {
        return [
            'old_password' => ['required', 'string', function (string $attribute, mixed $value, Closure $fail): void {
                $user = $this->user();

                if ($user === null || ! Hash::check((string) $value, $user->getAuthPassword())) {
                    $fail(__('The current password is incorrect.'));
                }
            }],
            'password' => ['required', 'confirmed', new SecurePassword],
        ];
    }

    public function handle()
    {
        $user = $this->user();

        $user->forceFill(['password' => Hash::make((string) $this->input('password'))])->save();

        event(new PasswordReset($user));

        $message = __('Your password has been updated.');

        return $this->wantsJson()
            ? response()->json(['success' => true, 'message' => $message])
            : redirect(config('laravel-auth.routes.redirects.update-password'))->with('success', $message);
    }
}
