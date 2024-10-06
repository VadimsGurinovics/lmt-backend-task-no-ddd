<?php

use App\Http\Middleware\ApiKeyMiddleware;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'api.key' => ApiKeyMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Handle ModelNotFoundException globally
        $exceptions->renderable(function (ModelNotFoundException $e, $request) {
            return response()->json([
                'error' => 'Resource not found',
            ], 404);
        });

        // Handle ValidationException globally
        $exceptions->renderable(function (ValidationException $e, $request) {
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $e->errors(),
            ], 422);
        });

        // Handle QueryException globally
        $exceptions->renderable(function (QueryException $e, $request) {
            return response()->json([
                'error' => 'Database error: ' . $e->getMessage(),
            ], 500);
        });

        // Handle any other exceptions globally
        $exceptions->renderable(function (Throwable $e, $request) {
            return response()->json([
                'error' => 'Something went wrong: ' . $e->getMessage(),
            ], 500);
        });
    })
    ->create();
