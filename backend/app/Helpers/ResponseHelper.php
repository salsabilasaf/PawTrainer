<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\MessageBag;

class ResponseHelper
{
    public static function success(string $message, ?array $data = null, int $status = 200): JsonResponse
    {
        return response()->json(self::payload(true, $message, $data), $status);
    }

    public static function created(string $message, ?array $data = null): JsonResponse
    {
        return self::success($message, $data, 201);
    }

    public static function error(string $message, mixed $errors = null, int $status = 400): JsonResponse
    {
        $payload = self::payload(false, $message);

        if ($errors !== null) {
            $payload['errors'] = $errors instanceof MessageBag ? $errors->toArray() : $errors;
        }

        return response()->json($payload, $status);
    }

    public static function validationError(mixed $errors): JsonResponse
    {
        return self::error('Validasi gagal', $errors, 422);
    }

    public static function unauthorized(string $message = 'Unauthorized'): JsonResponse
    {
        return self::error($message, null, 401);
    }

    public static function forbidden(string $message = 'Forbidden'): JsonResponse
    {
        return self::error($message, null, 403);
    }

    public static function notFound(string $message = 'Data tidak ditemukan'): JsonResponse
    {
        return self::error($message, null, 404);
    }

    public static function serverError(string $message = 'Terjadi kesalahan pada server'): JsonResponse
    {
        return self::error($message, null, 500);
    }

    private static function payload(bool $success, string $message, ?array $data = null): array
    {
        $payload = [
            'success' => $success,
            'message' => $message,
        ];

        if ($data !== null) {
            $payload['data'] = $data;
        }

        return $payload;
    }
}
