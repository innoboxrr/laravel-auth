<?php

namespace Innoboxrr\LaravelAuth\Http\Controllers;

use Innoboxrr\LaravelAuth\Http\Requests\Impersonate\ImpersonateRequest;
use Innoboxrr\LaravelAuth\Http\Requests\Impersonate\ImpersonateTokenRequest;
use Innoboxrr\LaravelAuth\Http\Requests\Impersonate\RevertImpersonateRequest;

class ImpersonateController extends Controller
{
    
    public function impersonate(ImpersonateRequest $request)
    {
        return $request->handle();
    }

    public function impersonateToken(ImpersonateTokenRequest $request)
    {
        return $request->handle();
    }

    public function revertImpersonate(RevertImpersonateRequest $request)
    {
        return $request->handle();
    }

}
