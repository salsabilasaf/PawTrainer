<?php

namespace App\Exceptions;

use App\Helpers\ResponseHelper;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * Exception yang tidak perlu di-report ke log.
     */
    protected $dontReport = [
        //
    ];

    /**
     * Exception yang tidak perlu di-flash ke session.
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render semua exception ke JSON response yang konsisten.
     */
    public function render($request, Throwable $e)
    {
        // Selalu kembalikan JSON untuk API routes
        if ($request->is('api/*') || $request->expectsJson()) {
            return $this->handleApiException($e);
        }

        return parent::render($request, $e);
    }

    private function handleApiException(Throwable $e)
    {
        // ── JWT Exceptions ──────────────────────────────────────────────────
        if ($e instanceof TokenExpiredException) {
            return ResponseHelper::unauthorized('Token sudah kadaluarsa, silakan login kembali');
        }

        if ($e instanceof TokenInvalidException) {
            return ResponseHelper::unauthorized('Token tidak valid');
        }

        if ($e instanceof JWTException) {
            return ResponseHelper::unauthorized('Token tidak ditemukan');
        }

        // ── Laravel Auth ────────────────────────────────────────────────────
        if ($e instanceof AuthenticationException) {
            return ResponseHelper::unauthorized('Anda harus login terlebih dahulu');
        }

        // ── Validation ──────────────────────────────────────────────────────
        if ($e instanceof ValidationException) {
            return ResponseHelper::validationError($e->errors());
        }

        // ── Model Not Found ─────────────────────────────────────────────────
        if ($e instanceof ModelNotFoundException) {
            $model = class_basename($e->getModel());
            return ResponseHelper::notFound("{$model} tidak ditemukan");
        }

        // ── Route Not Found ─────────────────────────────────────────────────
        if ($e instanceof NotFoundHttpException) {
            return ResponseHelper::notFound('Endpoint tidak ditemukan');
        }

        // ── Method Not Allowed ──────────────────────────────────────────────
        if ($e instanceof MethodNotAllowedHttpException) {
            return ResponseHelper::error('HTTP method tidak diizinkan', null, 405);
        }

        // ── Generic Server Error ────────────────────────────────────────────
        return ResponseHelper::serverError(
            config('app.debug')
                ? $e->getMessage()
                : 'Terjadi kesalahan pada server, silakan coba lagi'
        );
    }
}
