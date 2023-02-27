<?php

namespace Innoboxrr\LaravelAuth\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class LogoutRequest extends FormRequest
{

    public function authorize()
    {
    
        return true;
    
    }

    public function rules()
    {
    
        return [];
    
    }

    public function handle()
    {
    
        Auth::guard('web')->logout();
    
        $this->session()->invalidate();
    
        $this->session()->regenerateToken();

        if ($this->wantsJson()) {
    
            return response()->json(['success' => true]);
    
        } else {
    
            return redirect(config('laravel-auth.routes.redirects.logout'));
    
        }
    
    }

}