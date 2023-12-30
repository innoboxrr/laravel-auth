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

            if (now()->timestamp - $this->timestamp > 60) return false;

            if ($this->originalUserId != $this->user()->id) return false;

            if (static::$authorizeImpersonateCallback) return call_user_func(static::$authorizeImpersonateCallback, $this);

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
        Auth::loginUsingId($this->targetUserId);
        session()->put('impersonate_token', $this->token);
        $this->session()->regenerate();
        return redirect(config('laravel-auth.routes.redirects.impersonate-token'));
    }
    
}