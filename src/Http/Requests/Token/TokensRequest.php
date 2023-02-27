<?php

namespace Innoboxrr\LaravelAuth\Http\Requests\Token;

use Illuminate\Foundation\Http\FormRequest;

class TokensRequest extends FormRequest
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
        $tokens = $this->user()->tokens;

        return $this->getResponse($tokens);
    }

    public function getResponse($tokens)
    {

        if ($this->wantsJson()) {

            return response()->json(['tokens' => $tokens]);

        } else {

            return redirect(config('laravel-auth.routes.redirects.tokens'))
                ->with('tokens', $tokens);

        }
    
    }

}
