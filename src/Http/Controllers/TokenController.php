<?php

namespace Innoboxrr\LaravelAuth\Http\Controllers;

use Innoboxrr\LaravelAuth\Http\Requests\Token\CreateTokenRequest;
use Innoboxrr\LaravelAuth\Http\Requests\Token\TokensRequest;
use Innoboxrr\LaravelAuth\Http\Requests\Token\RevokeTokenRequest;
use Innoboxrr\LaravelAuth\Http\Requests\Token\FlushTokensRequest;

class TokenController extends Controller
{
    
    public function createToken(CreateTokenRequest $request)
    {
        return $request->handle();
    }

    public function tokens(TokensRequest $request)
    {
        return $request->handle();
    }

    public function revokeToken(RevokeTokenRequest $request)
    {
        return $request->handle();
    }

    public function flushTokens(FlushTokensRequest $request)
    {
        return $request->handle();
    }

}
