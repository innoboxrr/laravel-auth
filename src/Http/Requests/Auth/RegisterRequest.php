<?php

namespace Innoboxrr\LaravelAuth\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;
use Innoboxrr\LaravelAuth\Rules\SecurePassword;
use Illuminate\Support\Facades\Hash;

class RegisterRequest extends FormRequest
{

    public function authorize(): bool
    {

        return config('laravel-auth.allow-registration', true);

    }

    public function rules(): array
    {

        return [
            
            'name' => ['required', 'string', 'max:255'],
            
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],

            'password' => ['required', 'confirmed', new SecurePassword],

        ];

    }

    protected function passedValidation()
    {

        $this->merge([
            
            'password' => Hash::make($this->password)

        ]);

    }

    protected function resolveUserInstance()
    {

        $userClass = config('laravel-auth.user-class');

        $userClass = app($userClass);

        return new $userClass();

    }
    
    public function handle()
    {

        $user = $this->resolveUserInstance()->create($this->all());

        event(new Registered($user));

        Auth::login($user);

        return $this->wantsJson()
            ? response()->json(['success' => true])
            : redirect(config('laravel-auth.routes.redirects.register'));

    }
    
}