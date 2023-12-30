<?php

namespace Innoboxrr\LaravelAuth\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class RevertImpersonateRequest extends FormRequest
{

    public function authorize(): bool
    {
        return session()->has('impersonate_token');
    }

    public function rules(): array
    {
        return [];
    }

    public function handle()
    {
        try {
            $sessionData = decrypt(session('impersonate_token'));
            $originalUserId = $sessionData['original_user_id'];
            $timestamp = $sessionData['timestamp'];
            $status = false;

            if (now()->timestamp - $timestamp > 7200) {
                Auth::logout();
                session()->forget('impersonate_token');
                $status = false;
            } else {
                Auth::loginUsingId($originalUserId);
                session()->forget('impersonate_token');
                $status = true;
            }

            return $this->wantsJson()
            ? response()->json(['status' => $status])
            : redirect(config('laravel-auth.routes.redirects.revert-impersonate'));

        } catch (\Exception $e) {
            Auth::logout();
            abort(500);
        }
    }
    
}