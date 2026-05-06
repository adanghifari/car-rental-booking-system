<?php

use App\Http\Responses\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $exception, Request $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            if ($exception instanceof ValidationException) {
                return ApiResponse::validation($exception->errors(), $exception->getMessage());
            }

            if ($exception instanceof AuthenticationException) {
                return ApiResponse::unauthorized('Unauthenticated.');
            }

            if ($exception instanceof AuthorizationException) {
                return ApiResponse::forbidden('Forbidden.');
            }

            if ($exception instanceof ModelNotFoundException) {
                return ApiResponse::notFound('Resource not found.');
            }

            if ($exception instanceof HttpExceptionInterface) {
                $message = $exception->getMessage() ?: 'Request failed.';

                return ApiResponse::error($message, $exception->getStatusCode());
            }

            return ApiResponse::error('Server error.', 500);
        });
    })->create();