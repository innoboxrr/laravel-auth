<?php

namespace Innoboxrr\LaravelAuth\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use App\Providers\RouteServiceProvider;

class EmailVerificationNotificationRequest extends FormRequest
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

        if ($this->user()->hasVerifiedEmail()) {

            return $this->getResponse(true);

        }

        $this->user()->sendEmailVerificationNotification();

        return $this->getResponse('verification-link-sent');

    }

    public function getResponse($status)
    {

        if ($this->wantsJson()) {

            return response()->json(['success' => $status]);

        } else {

            return redirect(config('laravel-auth.routes.redirects.email-verification-notification'));

        }

    }
    
}
