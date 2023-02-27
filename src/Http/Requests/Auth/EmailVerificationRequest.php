<?php

namespace Innoboxrr\LaravelAuth\Http\Requests\Auth;

use Illuminate\Foundation\Auth\EmailVerificationRequest as VerificationRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;

class EmailVerificationRequest extends VerificationRequest
{

    public function handle()
    {

        if ($this->user()->hasVerifiedEmail()) {
        
            return $this->getResponse();

        }

        if ($this->user()->markEmailAsVerified()) {
            
            event(new Verified($this->user()));

        }

        return $this->getResponse();

    }

    public function getResponse()
    {

        if($this->wantsJson()) {

            return response()->json(['verified' => true]);
            
        } else {

            return redirect()->intended(
                config('app.frontend_url').RouteServiceProvider::HOME.'?verified=1'
            );

        }

    }
    
}