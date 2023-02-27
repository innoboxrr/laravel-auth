<?php

namespace Innoboxrr\LaravelAuth\Http\Requests\Token;

use Illuminate\Foundation\Http\FormRequest;

class FlushTokensRequest extends FormRequest
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

        $this->user()->tokens()->delete();
        
        return $this->getResponse();

    }

    public function getResponse()
    {

        if ($this->wantsJson()) {
       
            return response()->json(['message' => 'All tokens have been revoked']);
        
        } else {

            return redirect()->back()->with('status', 'All tokens have been revoked');
        }

    }

}