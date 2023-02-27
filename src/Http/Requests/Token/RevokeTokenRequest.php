<?php

namespace Innoboxrr\LaravelAuth\Http\Requests\Token;

use Illuminate\Foundation\Http\FormRequest;

class RevokeTokenRequest extends FormRequest
{

    public function authorize()
    {

        return true;

    }

    public function rules()
    {
        return [
            'token_id' => ['required', 'exists:personal_access_tokens,id'],
        ];
    }

    public function handle()
    {

        $this->user()->tokens()->where('id', $this->token_id)->delete();

        return $this->getResponse();

    }

    public function getResponse()
    {
        if ($this->wantsJson()) {

            return response()->json(['revoked' => true]);

        } else {

            return redirect(config('laravel-auth.routes.redirects.revoke-token'))
                ->with('revoked', true);

        }

    }
    
}