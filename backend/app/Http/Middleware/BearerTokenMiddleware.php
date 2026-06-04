<?php

namespace App\Http\Middleware;

use App\Helpers\ResponseHelper;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Symfony\Component\HttpFoundation\Response;

class BearerTokenMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            if (!$request->bearerToken()) {
                return ResponseHelper::unauthorized('Token tidak ditemukan');
            }

            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return ResponseHelper::unauthorized('Token tidak valid atau sudah kadaluarsa');
            }

            Auth::shouldUse('api');
            Auth::setUser($user);
            $request->setUserResolver(fn () => $user);
        } catch (JWTException $e) {
            return ResponseHelper::unauthorized('Token tidak valid atau sudah kadaluarsa');
        }

        return $next($request);
    }
}
