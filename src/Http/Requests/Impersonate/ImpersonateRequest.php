<?php

namespace Innoboxrr\LaravelAuth\Http\Requests\Impersonate;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

/**
 * Pide un token para entrar como otro usuario.
 *
 * El token cifra quién lo pidió, a quién y cuándo, y sólo sirve a quien lo pidió
 * durante dos minutos (ver ImpersonateTokenRequest).
 */
class ImpersonateRequest extends FormRequest
{
    protected static $authorizeImpersonateCallback = null;

    protected static $customRulesCallback = null;

    /**
     * Sustituye por completo la autorización. La propiedad existía, pero no
     * había forma de asignarla desde la aplicación.
     */
    public static function authorizeUsing(?callable $callback): void
    {
        static::$authorizeImpersonateCallback = $callback;
    }

    public static function setCustomRulesCallback(?callable $callback): void
    {
        static::$customRulesCallback = $callback;
    }

    public function authorize(): bool
    {
        if (! config('laravel-auth.allow-impersonate', true) || ! $this->user()) {
            return false;
        }

        if (static::$authorizeImpersonateCallback) {
            return (bool) call_user_func(static::$authorizeImpersonateCallback, $this);
        }

        // Sin destino todavía se comprueba quién pide: si el id falta o no
        // existe, responde la validación con un 422.
        return Gate::forUser($this->user())->allows('laravel-auth.impersonate', [$this->target()]);
    }

    public function rules(): array
    {
        if (static::$customRulesCallback) {
            return call_user_func(static::$customRulesCallback, $this);
        }

        $model = app(config('laravel-auth.user-class'));

        return [
            'target_user_id' => ['required', Rule::exists($model->getTable(), $model->getKeyName())],
        ];
    }

    public function target(): ?Authenticatable
    {
        if (! $this->filled('target_user_id')) {
            return null;
        }

        $userClass = config('laravel-auth.user-class');

        return $userClass::find($this->input('target_user_id'));
    }

    public function handle()
    {
        $token = encrypt([
            'original_user_id' => $this->user()->getAuthIdentifier(),
            'target_user_id' => $this->input('target_user_id'),
            'timestamp' => now()->timestamp,
        ]);

        return response()->json([
            'token' => $token,
            'url' => route(config('laravel-auth.routes.as').config('laravel-auth.routes.names.impersonate-token'), ['token' => $token]),
        ]);
    }
}
