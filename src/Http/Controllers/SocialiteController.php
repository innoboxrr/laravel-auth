<?php

namespace Innoboxrr\LaravelAuth\Http\Controllers;

use Innoboxrr\LaravelAuth\Http\Requests\Socialite\RedirectRequest;
use Innoboxrr\LaravelAuth\Http\Requests\Socialite\CallbackRequest;

class SocialiteController extends Controller
{
    
    public function redirect(RedirectRequest $request)
    {
        return $request->handle();
    }

    public function callback(CallbackRequest $request)
    {
        return $request->handle();
    }

}
