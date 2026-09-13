<?php

namespace Innoboxrr\LaravelAuth\Http\Requests\Token;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Crea un token de Sanctum para una integración sin sesión.
 *
 * Antes hacía `Auth::attempt`, que además inicia sesión en el navegador que
 * pide el token; con credenciales malas no devolvía nada, un 200 vacío; y la
 * caducidad se leía de `expires_at` aunque la regla validaba
 * `expiration_date`. Se aceptan los dos nombres.
 */
class CreateTokenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'name' => ['required', 'string', 'max:255'],
            'abilities' => ['nullable', 'array'],
            'abilities.*' => ['string'],
            'expires_at' => ['nullable', 'date', 'after:now'],
            'expiration_date' => ['nullable', 'date', 'after:now'],
        ];
    }

    public function handle()
    {
        $userClass = config('laravel-auth.user-class');

        $user = $userClass::where('email', $this->input('email'))->first();

        if ($user === null || ! Hash::check((string) $this->input('password'), $user->getAuthPassword())) {
            throw ValidationException::withMessages(['email' => __('auth.failed')]);
        }

        $expiresAt = $this->input('expires_at', $this->input('expiration_date'));

        $token = $user->createToken(
            (string) $this->input('name'),
            $this->input('abilities') ?: ['*'],
            $expiresAt ? Carbon::parse($expiresAt) : null
        );

        return $this->getResponse($token->plainTextToken);
    }

    public function getResponse(string $token)
    {
        return $this->wantsJson()
            ? response()->json(['token' => $token])
            : redirect(config('laravel-auth.routes.redirects.create-token'))->with('token', $token);
    }
}
