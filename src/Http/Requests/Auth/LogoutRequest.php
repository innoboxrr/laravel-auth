<?php

namespace Innoboxrr\LaravelAuth\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class LogoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }

    public function handle()
    {
        Auth::guard('web')->logout();

        $this->session()->invalidate();

        $this->session()->regenerateToken();

        return $this->wantsJson()
            ? response()->json(['success' => true])
            : redirect(config('laravel-auth.routes.redirects.logout'));
    }
}
