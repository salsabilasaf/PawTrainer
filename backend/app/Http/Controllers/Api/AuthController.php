<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    // ─── Register ─────────────────────────────────────────────────────────────

    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::validationError($validator->errors());
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'user', // Default role saat register
        ]);

        $token = JWTAuth::fromUser($user);

        return ResponseHelper::created('Registrasi berhasil', [
            'token' => $token,
            'user'  => $this->formatUser($user),
        ]);
    }

    // ─── Login ────────────────────────────────────────────────────────────────

    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::validationError($validator->errors());
        }

        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return ResponseHelper::unauthorized('Email atau password salah');
            }
        } catch (JWTException $e) {
            return ResponseHelper::serverError('Gagal membuat token, silakan coba lagi');
        }

        $user = auth()->user();

        return ResponseHelper::success('Login berhasil', [
            'token'      => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60, // dalam detik
            'user'       => $this->formatUser($user),
        ]);
    }

    // ─── Logout ───────────────────────────────────────────────────────────────

    public function logout(): JsonResponse
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
        } catch (JWTException $e) {
            return ResponseHelper::serverError('Gagal logout, silakan coba lagi');
        }

        return ResponseHelper::success('Logout berhasil');
    }

    // ─── Profile ──────────────────────────────────────────────────────────────

    public function profile(): JsonResponse
    {
        $user = auth()->user();

        return ResponseHelper::success('Data profil berhasil diambil', [
            'user' => $this->formatUser($user),
        ]);
    }

    // ─── Private Helper ───────────────────────────────────────────────────────

    private function formatUser(User $user): array
    {
        return [
            'id'         => $user->id,
            'name'       => $user->name,
            'email'      => $user->email,
            'role'       => $user->role,
            'created_at' => $user->created_at,
        ];
    }
}
