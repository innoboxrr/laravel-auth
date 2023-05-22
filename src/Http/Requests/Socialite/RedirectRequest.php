<?php

namespace Innoboxrr\LaravelAuth\Http\Requests\Socialite;

use Illuminate\Foundation\Http\FormRequest;
use App\Providers\RouteServiceProvider;
use Laravel\Socialite\Facades\Socialite;

class RedirectRequest extends FormRequest
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

        return Socialite::driver($this->driver)
            ->with(['action' => $this->action])
            ->redirect();

    }
    
}
