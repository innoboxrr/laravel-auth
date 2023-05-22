<?php

namespace Innoboxrr\LaravelAuth\Http\Requests\Socialite;

use Illuminate\Foundation\Http\FormRequest;
use App\Providers\RouteServiceProvider;
use Laravel\Socialite\Facades\Socialite;

class CallbackRequest extends FormRequest
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

        $providerUser = Socialite::driver($this->provider)->user();

        dd($providerUser);

    }   
    
}
