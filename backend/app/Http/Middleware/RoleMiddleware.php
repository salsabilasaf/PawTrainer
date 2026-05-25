<?php

namespace App\Http\Middleware;

use App\Helpers\ResponseHelper;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Periksa apakah user memiliki role yang diizinkan.
     *
     * Contoh penggunaan di route:
     *   ->middleware('role:admin')
     *   ->middleware('role:admin,user')
     *
     * @param  string  $roles  Comma-separated list of allowed roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = auth()->user();

        // Pastikan user sudah ter-autentikasi
        if (!$user) {
            return ResponseHelper::unauthorized('Token tidak valid atau sudah kadaluarsa');
        }

        // Cek apakah role user ada di daftar role yang diizinkan
        if (!in_array($user->role, $roles)) {
            return ResponseHelper::forbidden(
                'Anda tidak memiliki akses. Role yang dibutuhkan: ' . implode(' atau ', $roles)
            );
        }

        return $next($request);
    }
}
