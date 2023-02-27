<?php

namespace Innoboxrr\LaravelAuth\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{

    public function authorize(): bool
    {

        return true;

    }

    public function rules(): array
    {

        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];

    }

    public function authenticate(): void
    {
        
        $this->ensureIsNotRateLimited();

        $email = $this->input('email');

        $password = $this->input('password');

        $remember = $this->boolean('remember');

        if (! Auth::attempt(compact('email', 'password'), $remember)) {

            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);

        }

        RateLimiter::clear($this->throttleKey());

    }

    public function ensureIsNotRateLimited(): void
    {

        $throttleKey = $this->throttleKey();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {

            event(new Lockout($this));
            
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'email' => trans('auth.throttle', [
                    'seconds' => $seconds,
                    'minutes' => ceil($seconds / 60),
                ]),
            ]);

        }

    }

    public function throttleKey(): string
    {
        
        $email = $this->input('email');
        
        $ip = $this->ip();
        
        return Str::transliterate(Str::lower("$email|$ip"));

    }

    public function handle()
    {

        $this->authenticate();

        $this->session()->regenerate();

        return $this->wantsJson()
            ? response()->json(['success' => true])
            : redirect(config('laravel-auth.routes.redirects.login'));

    }
    
}