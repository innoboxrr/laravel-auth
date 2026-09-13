<?php

namespace Innoboxrr\LaravelAuth\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Password;

class ForgotPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
        ];
    }

    /**
     * Responde lo mismo exista o no la cuenta, e incluso si se frenó por pedir
     * el enlace varias veces seguidas.
     *
     * Antes decía "No pudimos encontrar un usuario con esa dirección": la
     * pantalla servía para averiguar qué correos tienen cuenta.
     */
    public function handle()
    {
        Password::sendResetLink($this->only('email'));

        $message = __('If the address is registered, we have emailed a password reset link.');

        return $this->wantsJson()
            ? response()->json(['success' => true, 'message' => $message])
            : back()->with('status', $message);
    }
}
