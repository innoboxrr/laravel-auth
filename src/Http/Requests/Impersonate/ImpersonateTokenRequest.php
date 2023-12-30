<?php

namespace Innoboxrr\LaravelAuth\Http\Requests\Impersonate;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ImpersonateTokenRequest extends FormRequest
{

    protected static $authorizeImpersonateCallback = null;

    protected $tokenPayload;

    protected $originalUserId;

    protected $targetUserId;

    protected $timestamp;

    protected function prepareForValidation()
    {
        try {
            $this->tokenPayload = decrypt($this->token);
            $this->originalUserId = $this->tokenPayload['original_user_id'];
            $this->targetUserId = $this->tokenPayload['target_user_id'];
            $this->timestamp = $this->tokenPayload['timestamp'];
        } catch (\Exception $e) {
            $this->tokenPayload = null;
            $this->originalUserId = null;
            $this->targetUserId = null;
            $this->timestamp = null;
        }
    }

    public function authorize(): bool
    {
        try {

            if (now()->timestamp - $this->timestamp > 120) return false;

            if (static::$authorizeImpersonateCallback) 
                return call_user_func(static::$authorizeImpersonateCallback, $this);

            return true;

        } catch (\Exception $e) {

            return false;

        }
    }

    public function rules(): array
    {
        return [
            // 
        ];
    }

    public function handle()
    {
        $userClass = app(config('laravel-auth.user-class'));

        $targetUser = $userClass::findOrFail($this->targetUserId);

        auth()->login($targetUser);

        session()->put('impersonate_token', $this->token);

        return view('laravel-auth::impersonate', [
            'token' => $this->token,
            'target_user' => $targetUser,
            'timestamp' => $this->timestamp + 120,
        ]);
    }
    
}