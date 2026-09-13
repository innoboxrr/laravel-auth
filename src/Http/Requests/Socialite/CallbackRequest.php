<?php

namespace Innoboxrr\LaravelAuth\Http\Requests\Socialite;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class CallbackRequest extends FormRequest
{
    public static $customLoginCallback;

    public static $customRegisterCallback;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }

    /**
     * Entra en la cuenta del correo que devuelve el proveedor, o la crea.
     *
     * La aplicación puede sustituir cualquiera de los dos casos con
     * `$customLoginCallback` y `$customRegisterCallback`.
     */
    public function handle()
    {
        $provider = (string) $this->route('provider');

        abort_unless(is_array(config("services.{$provider}")), 404);

        $providerUser = Socialite::driver($provider)->stateless()->user();

        $userClass = config('laravel-auth.user-class');

        $user = $userClass::where('email', $providerUser->getEmail())->first();

        if ($user !== null && static::$customLoginCallback) {
            return call_user_func(static::$customLoginCallback, $user, $provider, $providerUser);
        }

        if ($user === null && static::$customRegisterCallback) {
            return call_user_func(static::$customRegisterCallback, $providerUser, $provider);
        }

        $user ??= $userClass::create([
            'name' => $providerUser->getName() ?: $providerUser->getEmail(),
            'email' => $providerUser->getEmail(),
            'password' => Hash::make(Str::random(40)),
        ]);

        Auth::guard('web')->login($user);

        if ($this->hasSession()) {
            $this->session()->regenerate();
        }

        return $this->wantsJson()
            ? response()->json(['success' => true, 'user' => $user])
            : redirect(config('laravel-auth.routes.redirects.socialite-callback'));
    }
}
