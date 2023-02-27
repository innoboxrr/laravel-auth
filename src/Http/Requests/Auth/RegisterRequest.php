<?php

namespace Innoboxrr\LaravelAuth\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;
use Innoboxrr\LaravelAuth\Rules\SeccurePassword;
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
            
            'email' => ['required', 'string', 'email', 'max:255', 'unique:App\Models\User'],

            'password' => ['required', 'confirmed', new SeccurePassword],

        ];

    }

    protected function passedValidation()
    {

        $this->merge([
            'password' => Hash::make($this->password)
        ]);

    }

    public function handle()
    {

        $user = User::create($this->all());

        event(new Registered($user));

        Auth::login($user);

        return $this->wantsJson()
            ? response()->json(['success' => true])
            : redirect(config('laravel-auth.routes.redirects.register'));

    }
    
}