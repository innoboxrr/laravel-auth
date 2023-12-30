<?php

namespace Innoboxrr\LaravelAuth\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ActAsRequest extends FormRequest
{

    protected static $actAsCallback = null;

    protected static $customRulesCallback = null;

    public function authorize(): bool
    {
        if(static::$actAsCallback) {
            return call_user_func(static::$actAsCallback, $this);
        }

        return config('laravel-auth.allow-act-as', true);
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
        $userClass = app(config('laravel-auth.user-class', 'App\\Models\User'));

        $targetUser = $userClass::findOrFail($this->target_user_id);

        Auth::login($targetUser);

        if (!session()->has('original_user_id')) {
            $originalUserId = $this->user()->id;
            $timestamp = now()->timestamp; // Obtener el timestamp actual
            $encryptedData = encrypt(['user_id' => $originalUserId, 'timestamp' => $timestamp]);

            session(['original_user_id' => $encryptedData]);
        }

        return $targetUser;
    }
    
}