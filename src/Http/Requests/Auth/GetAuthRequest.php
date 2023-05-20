<?php

namespace Innoboxrr\LaravelAuth\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class GetAuthRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [];
    }

    public function getResponse()
    {

        if ($this->wantsJson()) {

            return response()->json([
                'user' => $this->user()
            ]);

        } else {

            return redirect(config('laravel-auth.routes.redirects.get-auth'))
                ->with('user', $this->user());

        }
    
    }
    
    public function handle()
    {

        return $this->getResponse();

    }

}
