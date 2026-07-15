<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__ . '/../routes/api.php',
        apiPrefix: 'api',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
    // No stateful API (we use token auth)
})
    ->withExceptions(function (Exceptions $exceptions) {

        // Always return JSON for API routes
        $exceptions->render(function (Throwable $e, Request $request) {

            if (!$request->is('api/*')) {
                return null; // Let default handler deal with web routes
            }

            if ($e instanceof AuthenticationException) {
                return response()->json([
                    'message' => 'Non authentifié. Veuillez vous connecter.',
                ], 401);
            }

            if ($e instanceof ValidationException) {
                return response()->json([
                    'message' => 'Les données fournies sont invalides.',
                    'errors'  => $e->errors(),
                ], 422);
            }

            if ($e instanceof NotFoundHttpException) {
                return response()->json([
                    'message' => 'Ressource introuvable.',
                ], 404);
            }

            if ($e instanceof HttpException) {
                return response()->json([
                    'message' => $e->getMessage() ?: 'Erreur HTTP.',
                ], $e->getStatusCode());
            }

            // Generic server error
            $status  = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
            $message = app()->isProduction()
                ? 'Une erreur interne est survenue.'
                : $e->getMessage();

            return response()->json(['message' => $message], $status);
        });
    })
    ->create();
