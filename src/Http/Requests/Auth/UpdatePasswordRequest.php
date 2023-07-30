<?php

namespace Innoboxrr\LaravelAuth\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Innoboxrr\LaravelAuth\Rules\SecurePassword;
use Illuminate\Auth\Events\PasswordUpdated;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UpdatePasswordRequest extends FormRequest
{

    public function authorize(): bool
    {

        return true;

    }

    public function rules(): array
    {

        return [

            'old_password' => ['required'],

            'password' => ['required', 'confirmed', new SecurePassword],

        ];

    }

    public function handle()
    {
        // Obtener el usuario autenticado
        $user = $this->user();

        // Verificar que la contraseña anterior proporcionada coincida con la contraseña actual del usuario
        if (!Hash::check($this->input('old_password'), $user->password)) {
            if ($this->wantsJson()) {
                return response()->json(['error' => 'La contraseña anterior no es válida.'], 422);
            }
            return back()->withErrors(['old_password' => 'La contraseña anterior no es válida.']);
        }

        // Actualizar la contraseña del usuario con la nueva contraseña proporcionada
        $user->update([
            'password' => Hash::make($this->input('password')),
        ]);

        event(new PasswordUpdated($user));

        if ($this->wantsJson()) {
            
            return response()->json(['message' => 'Contraseña actualizada correctamente.'], 200);

        }

        // Redirigir a una ruta específica con un mensaje de éxito
        return redirect(config('laravel-auth.routes.redirects.update-password'))
            ->with('success', 'Contraseña actualizada correctamente.');
    }
    
}