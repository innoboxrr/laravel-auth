<?php

namespace Innoboxrr\LaravelAuth\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ActAsRequest extends FormRequest
{

    public function authorize(): bool
    {
        return session()->has('original_user_id');
    }

    public function rules(): array
    {
        return [];
    }

    public function handle()
    {
        try {
            $sessionData = decrypt(session('original_user_id'));
            $originalUserId = $sessionData['user_id'];
            $timestamp = $sessionData['timestamp'];

            if (now()->timestamp - $timestamp > 3600) {
                Auth::logout();
                session()->forget('original_user_id');
                return false;
            }

            Auth::loginUsingId($originalUserId);
            session()->forget('original_user_id');
            return true;
        } catch (\Exception $e) {
            Auth::logout();
            return false;
        }
        return false;
    }
    
}