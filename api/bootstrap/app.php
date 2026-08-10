<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->respond(function (\Illuminate\Http\Response $response, \Throwable $exception, \Illuminate\Http\Request $request) {
            if ($request->is('api/*')) {
                if ($response->status() === 419) {
                    return response()->json([
                        'message' => 'Session expired. Please login again.',
                    ], 419);
                }
                
                if ($response->status() === 401) {
                    return response()->json([
                        'message' => 'Unauthenticated.',
                    ], 401);
                }
                
                if ($response->status() === 403) {
                    return response()->json([
                        'message' => 'Forbidden. You do not have permission to access this resource.',
                    ], 403);
                }
                
                if ($response->status() === 404) {
                    return response()->json([
                        'message' => 'Resource not found.',
                    ], 404);
                }
            }
            
            return $response;
        });
    })->create();
