<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

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
    ->withExceptions(using: function (Exceptions $exceptions): void {
            $exceptions->render(using: function (Throwable $e, Request $request) {

                // Для API-маршрутов возвращаем JSON
                if (str_starts_with($request->path(), 'api/')) {
                    return response()->json([
                        'message' => $e->getMessage(),
                        'error' => class_basename($e),
                    ], method_exists($e, 'getStatusCode')
                        ? $e->getStatusCode()
                        : 500);
                }
            });
    })->create();
