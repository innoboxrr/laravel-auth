<?php

namespace Innoboxrr\LaravelAuth\Http\Controllers;

use Innoboxrr\LaravelAuth\Http\Requests\Auth\LoginRequest;
use Innoboxrr\LaravelAuth\Http\Requests\Auth\RegisterRequest;
use Innoboxrr\LaravelAuth\Http\Requests\Auth\LogoutRequest;
use Innoboxrr\LaravelAuth\Http\Requests\Auth\ForgotPasswordRequest;
use Innoboxrr\LaravelAuth\Http\Requests\Auth\ResetPasswordRequest;
use Innoboxrr\LaravelAuth\Http\Requests\Auth\UpdatePasswordRequest;
use Innoboxrr\LaravelAuth\Http\Requests\Auth\EmailVerificationNotificationRequest as EVNRequest;
use Innoboxrr\LaravelAuth\Http\Requests\Auth\VerificationVerifyRequest;
use Innoboxrr\LaravelAuth\Http\Requests\Auth\GetAuthRequest;
use Innoboxrr\LaravelAuth\Http\Requests\Auth\ActAsRequest;
use Innoboxrr\LaravelAuth\Http\Requests\Auth\ReturnToOriginalUserRequest;

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

    public function updatePassword(UpdatePasswordRequest $request)
    {
        return $request->handle();
    }

    public function emailVerificationNotification(EVNRequest $request)
    {
        return $request->handle();
    }

    public function verificationVerify(VerificationVerifyRequest $request)
    {
        return $request->handle();
    }

    public function getAuth(GetAuthRequest $request)
    {
        return $request->handle();
    }

    public function actAs(ActAsRequest $request)
    {
        return $request->handle();
    }

    public function returnToOriginalUser(ReturnToOriginalUserRequest $request)
    {
        return $request->handle();
    }

}
