<?php

namespace Innoboxrr\LaravelAuth\Http\Requests\Auth;

use Closure;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\SessionGuard;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
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

        $this->keepSessionAuthenticated($user);

        event(new PasswordReset($user));

        $message = __('Your password has been updated.');

        return $this->wantsJson()
            ? response()->json(['success' => true, 'message' => $message])
            : redirect(config('laravel-auth.routes.redirects.update-password'))->with('success', $message);
    }

    /**
     * AuthenticateSession (el de Laravel y el que Sanctum pone en las rutas
     * stateful) guarda en la sesión la huella de la contraseña y cierra la
     * sesión si deja de coincidir. Sin actualizarla, quien cambiaba su
     * contraseña desde la SPA quedaba fuera en la siguiente petición a la API.
     */
    protected function keepSessionAuthenticated(Authenticatable $user): void
    {
        if (! $this->hasSession()) {
            return;
        }

        $guards = array_unique([Auth::getDefaultDriver(), ...Arr::wrap(config('sanctum.guard', 'web'))]);

        foreach ($guards as $name) {
            $guard = Auth::guard($name);

            if (! $guard instanceof SessionGuard || $guard->id() !== $user->getAuthIdentifier()) {
                continue;
            }

            $this->session()->put(
                "password_hash_{$name}",
                $guard->hashPasswordForCookie($user->getAuthPassword())
            );
        }
    }
}
