<?php

namespace Innoboxrr\LaravelAuth\Http\Requests\Socialite;

use Illuminate\Foundation\Http\FormRequest;
use Laravel\Socialite\Facades\Socialite;

class RedirectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }

    /**
     * Un proveedor sin credenciales en config/services.php responde 404. Antes
     * Socialite lanzaba una excepción: un 500 por un botón que la aplicación
     * nunca configuró.
     */
    public function handle()
    {
        $provider = (string) $this->route('provider');

        abort_unless(is_array(config("services.{$provider}")), 404);

        return Socialite::driver($provider)->redirect();
    }
}
