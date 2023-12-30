<?php

namespace Innoboxrr\LaravelAuth\Http\Requests\Impersonate;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ImpersonateRequest extends FormRequest
{

    protected static $authorizeImpersonateCallback = null;

    protected static $customRulesCallback = null;

    public function authorize(): bool
    {
        if(static::$authorizeImpersonateCallback) {
            return call_user_func(static::$authorizeImpersonateCallback, $this);
        }

        return config('laravel-auth.allow-impersonate', true);
    }

    public function rules(): array
    {

        if (static::$customRulesCallback) {
            return call_user_func(static::$customRulesCallback, $this);
        }

        return [
            'target_user_id' => 'required|exists:users,id',
        ];
    }

    public function handle()
    {
        $payload = [
            'original_user_id' => $this->user()->id, 
            'target_user_id' => $this->target_user_id,
            'timestamp' => now()->timestamp
        ];

        $token = encrypt($payload);

        return response()->json([
            'token' => $token,
        ]);
    }
    
}