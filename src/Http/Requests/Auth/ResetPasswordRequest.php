<?php

namespace Innoboxrr\LaravelAuth\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Innoboxrr\LaravelAuth\Rules\SeccurePassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ResetRequest extends FormRequest
{

    public function authorize(): bool
    {

        return true;

    }

    public function rules(): array
    {

        return [
            
            'token' => ['required'],

            'email' => ['required', 'email'],

            'password' => ['required', 'confirmed', new SeccurePassword],

        ];

    }

    public function handle()
    {
        
        $email = $this->email;
        
        $password = $this->password;
        
        $passwordConfirmation = $this->password_confirmation;
        
        $token = $this->token;

        $data = compact('email', 'password', 'passwordConfirmation', 'token');

        $resetPassword = function ($user) use ($password) {

            $user->forceFill([
                'password' => Hash::make($password),
                'remember_token' => Str::random(60),
            ])->save();

            event(new PasswordReset($user));

        };

        $status = Password::reset($data, $resetPassword);

        if ($this->wantsJson()) {

            return response()->json([
                'success' => $status == Password::PASSWORD_RESET,
                'message' => __($status),
            ]);

        } 

        if ($status == Password::PASSWORD_RESET) {
            
            return redirect(config('laravel-auth.routes.redirects.reset-password'))
                        ->with('status', __($status));

        } else {
            
            return back()->withInput($this->only('email'))
                        ->withErrors(['email' => __($status)]);

        }

    }
    
}