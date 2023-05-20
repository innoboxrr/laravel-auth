<?php

namespace Innoboxrr\LaravelAuth\Http\Requests\Auth;

use Illuminate\Foundation\Auth\EmailVerificationRequest as VerificationRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;

class VerificationVerifyRequest extends VerificationRequest
{

    public function getResponse($status)
    {

        return $this->wantsJson()
            ? response()->json(['verified' => $status])
            : redirect(config('laravel-auth.routes.redirects.login'));

    }

    public function handle()
    {

        if ($this->user()->hasVerifiedEmail()) {
        
            return $this->getResponse(true);

        }

        if ($this->user()->markEmailAsVerified()) {
            
            event(new Verified($this->user()));

            return $this->getResponse(true);

        }

        return $this->getResponse(false);

    }
    
}