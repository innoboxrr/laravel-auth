<?php

namespace Innoboxrr\LaravelAuth\Http\Requests\Token;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class CreateTokenRequest extends FormRequest
{

    public function authorize()
    {

        return true;

    }

    public function rules()
    {

        return [
            'name' => ['required', 'string'],
            'abilities' => ['nullable', 'array'],
            'expiration_date' => ['nullable', 'date', 'after:today'],
        ];

    }

    public function handle()
    {

        $abilities = ($this->abilities) ? $this->abilities : ['*'];


        $token = $this->user()->createToken($this->name, $this->input('abilities'));

        if ($this->expires_at) {

            $token->token->expires_at = Carbon::parse($this->expires_at);

            $token->token->save();

        }

        return $this->getResponse($token->plainTextToken);

    }

    public function getResponse($token)
    {
        if ($this->wantsJson()) {

            return response()->json(['token' => $token]);

        } else {

            return redirect(config('laravel-auth.routes.redirects.create-token'))
                ->with('token', $token);;

        }

    }
    
}
