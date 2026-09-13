<?php

namespace Innoboxrr\LaravelAuth\Http\Requests\Impersonate;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

/**
 * Entra como el usuario del token.
 *
 * El token sólo sirve a quien lo pidió, dentro de los dos minutos siguientes, y
 * si esa persona todavía puede suplantar a ese usuario. Antes bastaba con tener
 * el token: quien lo interceptara entraba como el usuario, y la respuesta era
 * una página con Tailwind desde un CDN que había que abrir dos veces.
 */
class ImpersonateTokenRequest extends FormRequest
{
    /**
     * Segundos que vale un token.
     */
    public const TTL = 120;

    protected static $authorizeImpersonateCallback = null;

    protected ?array $tokenPayload = null;

    public static function authorizeUsing(?callable $callback): void
    {
        static::$authorizeImpersonateCallback = $callback;
    }

    protected function prepareForValidation(): void
    {
        try {
            $payload = decrypt((string) $this->route('token'));

            $this->tokenPayload = is_array($payload) ? $payload : null;
        } catch (\Throwable) {
            $this->tokenPayload = null;
        }
    }

    public function authorize(): bool
    {
        $payload = $this->tokenPayload;

        if ($payload === null || ! $this->user() || ! config('laravel-auth.allow-impersonate', true)) {
            return false;
        }

        if (now()->timestamp - (int) ($payload['timestamp'] ?? 0) > self::TTL) {
            return false;
        }

        if ((string) ($payload['original_user_id'] ?? '') !== (string) $this->user()->getAuthIdentifier()) {
            return false;
        }

        if (static::$authorizeImpersonateCallback) {
            return (bool) call_user_func(static::$authorizeImpersonateCallback, $this);
        }

        $target = $this->target();

        return $target !== null && Gate::forUser($this->user())->allows('laravel-auth.impersonate', [$target]);
    }

    public function rules(): array
    {
        return [];
    }

    public function target(): ?Authenticatable
    {
        $userClass = config('laravel-auth.user-class');

        return isset($this->tokenPayload['target_user_id']) ? $userClass::find($this->tokenPayload['target_user_id']) : null;
    }

    public function handle()
    {
        $target = $this->target();

        $this->session()->put('impersonate_token', (string) $this->route('token'));

        Auth::guard('web')->login($target);

        $this->session()->regenerate();

        return $this->wantsJson()
            ? response()->json(['success' => true, 'user' => $target])
            : redirect(config('laravel-auth.routes.redirects.impersonate-token', '/'));
    }
}
