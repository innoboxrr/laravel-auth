<?php

namespace Innoboxrr\LaravelAuth\Http\Requests\Token;

use Illuminate\Foundation\Http\FormRequest;

class TokensRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [];
    }

    public function handle()
    {
        return $this->getResponse($this->user()->tokens);
    }

    public function getResponse($tokens)
    {
        return $this->wantsJson()
            ? response()->json(['tokens' => $tokens])
            : redirect(config('laravel-auth.routes.redirects.tokens'))->with('tokens', $tokens);
    }
}
