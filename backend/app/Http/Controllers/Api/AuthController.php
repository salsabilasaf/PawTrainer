<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::validationError($validator->errors());
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
        ]);

        $token = $this->issueToken($user);

        return ResponseHelper::created('Registrasi berhasil', [
            'token' => $token,
            'user' => $this->formatUser($user),
        ]);
    }

    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::validationError($validator->errors());
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return ResponseHelper::unauthorized('Email atau password salah');
        }

        $token = $this->issueToken($user);

        return ResponseHelper::success('Login berhasil', [
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => null,
            'user' => $this->formatUser($user),
        ]);
    }

    public function logout(): JsonResponse
    {
        if ($user = auth()->user()) {
            $user->forceFill(['api_token' => null])->save();
        }

        return ResponseHelper::success('Logout berhasil');
    }

    public function profile(): JsonResponse
    {
        return ResponseHelper::success('Data profil berhasil diambil', [
            'user' => $this->formatUser(auth()->user()),
        ]);
    }

    private function issueToken(User $user): string
    {
        $token = Str::random(80);

        $user->forceFill([
            'api_token' => Hash::make($token),
        ])->save();

        return $token;
    }

    private function formatUser(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'created_at' => $user->created_at,
        ];
    }
}
