<?php

namespace App\Exceptions;

use App\Helpers\ResponseHelper;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontReport = [];

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

    public function render($request, Throwable $e)
    {
        if ($request->is('api/*') || $request->expectsJson()) {
            return $this->handleApiException($e);
        }

        return parent::render($request, $e);
    }

    private function handleApiException(Throwable $e)
    {
        if ($e instanceof AuthenticationException) {
            return ResponseHelper::unauthorized('Anda harus login terlebih dahulu');
        }

        if ($e instanceof ValidationException) {
            return ResponseHelper::validationError($e->errors());
        }

        if ($e instanceof ModelNotFoundException) {
            return ResponseHelper::notFound(class_basename($e->getModel()) . ' tidak ditemukan');
        }

        if ($e instanceof NotFoundHttpException) {
            return ResponseHelper::notFound('Endpoint tidak ditemukan');
        }

        if ($e instanceof MethodNotAllowedHttpException) {
            return ResponseHelper::error('HTTP method tidak diizinkan', null, 405);
        }

        $status = (int) $e->getCode();
        if ($status >= 400 && $status < 600) {
            return ResponseHelper::error($e->getMessage(), null, $status);
        }

        return ResponseHelper::serverError(
            config('app.debug')
                ? $e->getMessage()
                : 'Terjadi kesalahan pada server, silakan coba lagi'
        );
    }
}
