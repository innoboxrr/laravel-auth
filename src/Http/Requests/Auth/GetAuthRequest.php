<?php

namespace Innoboxrr\LaravelAuth\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class GetAuthRequest extends FormRequest
{
    public static $customGetAuthCallback;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }

    /**
     * Lo que una SPA necesita al arrancar para decidir qué enseña: quién es, si
     * administra, si verificó su correo y si está suplantando a alguien. Antes
     * sólo venía el usuario, y la aplicación lo completaba con etiquetas
     * <meta> escritas por Blade, que obligaban a recargar la página tras
     * entrar o salir.
     *
     * Se resuelve con el guard de Sanctum, que acepta la sesión y también un
     * token.
     */
    public function getResponse()
    {
        $user = $this->user('sanctum');

        if (! $this->wantsJson()) {
            return redirect(config('laravel-auth.routes.redirects.get-auth'))->with('user', $user);
        }

        return response()->json([
            'user' => $user,
            'authenticated' => $user !== null,
            'is_admin' => $user !== null && method_exists($user, 'isAdmin') && (bool) $user->isAdmin(),
            'verified' => $user !== null && method_exists($user, 'hasVerifiedEmail') && $user->hasVerifiedEmail(),
            'impersonating' => $this->hasSession() && $this->session()->has('impersonate_token'),
        ]);
    }

    public function handle()
    {
        if (static::$customGetAuthCallback) {
            return call_user_func(static::$customGetAuthCallback, $this->user('sanctum'));
        }

        return $this->getResponse();
    }
}
