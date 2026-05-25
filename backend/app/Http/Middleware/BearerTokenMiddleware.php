<?php

namespace App\Http\Middleware;

use App\Helpers\ResponseHelper;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class BearerTokenMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return ResponseHelper::unauthorized('Token tidak ditemukan');
        }

        $users = User::whereNotNull('api_token')->get();
        $user = $users->first(fn (User $candidate) => Hash::check($token, $candidate->api_token));

        if (!$user) {
            return ResponseHelper::unauthorized('Token tidak valid atau sudah kadaluarsa');
        }

        Auth::setUser($user);
        $request->setUserResolver(fn () => $user);

        return $next($request);
    }
}
