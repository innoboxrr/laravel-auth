<?php

namespace Innoboxrr\LaravelAuth\Http\Requests\Impersonate;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Vuelve a la cuenta de quien suplantaba.
 *
 * Una suplantación dura como mucho dos horas: pasado ese tiempo se cierra la
 * sesión en lugar de devolver la cuenta original, por si quien suplantaba ya no
 * está delante.
 */
class RevertImpersonateRequest extends FormRequest
{
    /**
     * Segundos que puede durar una suplantación.
     */
    public const MAX_DURATION = 7200;

    public function authorize(): bool
    {
        return $this->user() !== null && $this->session()->has('impersonate_token');
    }

    public function rules(): array
    {
        return [];
    }

    public function handle()
    {
        try {
            $payload = decrypt($this->session()->get('impersonate_token'));
        } catch (\Throwable) {
            $payload = null;
        }

        $this->session()->forget('impersonate_token');

        $userClass = config('laravel-auth.user-class');
        $original = is_array($payload) ? $userClass::find($payload['original_user_id'] ?? null) : null;
        $expired = ! is_array($payload) || now()->timestamp - (int) ($payload['timestamp'] ?? 0) > self::MAX_DURATION;

        if ($original === null || $expired) {
            Auth::guard('web')->logout();
            $this->session()->invalidate();
            $this->session()->regenerateToken();

            return $this->wantsJson()
                ? response()->json(['success' => false, 'user' => null])
                : redirect(config('laravel-auth.routes.redirects.logout', '/'));
        }

        Auth::guard('web')->login($original);

        $this->session()->regenerate();

        return $this->wantsJson()
            ? response()->json(['success' => true, 'user' => $original])
            : redirect(config('laravel-auth.routes.redirects.revert-impersonate', '/'));
    }
}
