<?php

namespace Innoboxrr\LaravelAuth\Http\Requests\Auth;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Innoboxrr\LaravelAuth\Rules\SecurePassword;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', new SecurePassword],
        ];
    }

    /**
     * Un token inválido responde 422 con el error en `email`. Antes era un 200
     * con `success: false`, y la pantalla tenía que leer el cuerpo para saber
     * que había fallado.
     */
    public function handle()
    {
        $status = Password::reset(
            $this->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, string $password): void {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return $this->wantsJson()
                ? response()->json(['success' => true, 'message' => __($status)])
                : redirect(config('laravel-auth.routes.redirects.reset-password'))->with('status', __($status));
        }

        if ($this->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => __($status),
                'errors' => ['email' => [__($status)]],
            ], 422);
        }

        return back()->withInput($this->only('email'))->withErrors(['email' => __($status)]);
    }
}
