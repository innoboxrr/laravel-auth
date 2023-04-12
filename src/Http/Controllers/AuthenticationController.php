<?php

namespace Innoboxrr\LaravelAuth\Http\Controllers;

use Innoboxrr\LaravelAuth\Http\Requests\Auth\LoginRequest;
use Innoboxrr\LaravelAuth\Http\Requests\Auth\RegisterRequest;
use Innoboxrr\LaravelAuth\Http\Requests\Auth\LogoutRequest;
use Innoboxrr\LaravelAuth\Http\Requests\Auth\ForgotPasswordRequest;
use Innoboxrr\LaravelAuth\Http\Requests\Auth\ResetPasswordRequest;
use Innoboxrr\LaravelAuth\Http\Requests\Auth\EmailVerificationNotificationRequest as EVNRequest;
use Innoboxrr\LaravelAuth\Http\Requests\Auth\EmailVerificationRequest;
use Innoboxrr\LaravelAuth\Http\Requests\Auth\GetAuthRequest;

class AuthenticationController extends Controller
{
    
    public function login(LoginRequest $request)
    {
        return $request->handle();
    }

    public function register(RegisterRequest $request)
    {
        return $request->handle();
    }

    public function logout(LogoutRequest $request)
    {
        return $request->handle();
    }

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        return $request->handle();
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        return $request->handle();
    }

    public function emailVerificationNotification(EVNRequest $request)
    {
        return $request->handle();
    }

    public function emailVerification(EmailVerificationRequest $request)
    {
        return $request->handle();
    }

    public function getAuth(GetAuthRequest $request)
    {
        return $request->handle();
    }

}
