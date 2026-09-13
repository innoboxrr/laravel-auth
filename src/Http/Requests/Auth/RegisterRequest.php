<?php

namespace Innoboxrr\LaravelAuth\Http\Requests\Auth;

use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Innoboxrr\LaravelAuth\Rules\SecurePassword;

class RegisterRequest extends FormRequest
{
    protected static $customRulesCallback = null;

    public function authorize(): bool
    {
        return (bool) config('laravel-auth.allow-registration', true);
    }

    public function rules(): array
    {
        if (static::$customRulesCallback) {
            return call_user_func(static::$customRulesCallback, $this);
        }

        $model = $this->resolveUserInstance();

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.$model->getTable().',email'],
            'password' => ['required', 'confirmed', new SecurePassword],
        ];
    }

    public static function setCustomRulesCallback(?callable $callback): void
    {
        static::$customRulesCallback = $callback;
    }

    protected function resolveUserInstance()
    {
        return app(config('laravel-auth.user-class'));
    }

    /**
     * Crea la cuenta con lo que envía el formulario —el modelo decide qué acepta
     * con su $fillable—, con la contraseña cifrada una sola vez, y entra.
     */
    public function handle()
    {
        $data = $this->except(['password_confirmation', '_token', 'remember', 'redirect']);
        $data['password'] = Hash::make((string) $this->input('password'));

        $user = $this->resolveUserInstance()->create($data);

        event(new Registered($user));

        Auth::guard('web')->login($user, $this->boolean('remember'));

        if ($this->hasSession()) {
            $this->session()->regenerate();
        }

        return $this->wantsJson()
            ? response()->json(['success' => true, 'user' => $user])
            : redirect(config('laravel-auth.routes.redirects.register'));
    }
}
