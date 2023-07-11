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

    public function handle()
    {
        
        $status = Password::sendResetLink($this->only('email'));

        if ($status == Password::RESET_LINK_SENT) {

            $status = 'success';
        
            $message = __('Se ha enviado un enlace de restablecimiento de contraseña a su dirección de correo electrónico.');
        
        } else {

            $status = 'error';
        
            $message = __('No pudimos encontrar un usuario con esa dirección de correo electrónico.');
        
        }

        if ($this->wantsJson()) {
        
            return response()->json(['status' => $status, 'message' => $message]);
        
        } else {
        
            return back()->with(['status' => $status, 'message' => $message]);
        
        }

    }
    
}