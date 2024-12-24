<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Contracts\Auth\Factory as Auth;


class OptionalAuth extends Authenticate
{
    public function handle($request, Closure $next, ...$guards)
    {
        try {
            $this->authenticate($request, ['sanctum']);
        } catch (AuthenticationException $e) {
            // dont do anything
        }

        return $next($request);
    }
}
