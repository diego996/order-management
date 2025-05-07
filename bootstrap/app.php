<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Http\Middleware\ForceJsonResponse;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        //spiegamele
        // 1. ForceJsonResponse: Forza la risposta in JSON per tutte le richieste API
        // 2. EnsureFrontendRequestsAreStateful: Assicura che le richieste API siano stateful per il frontend
        // 3. throttle:api: Limita il numero di richieste API per evitare abusi
        // 4. auth:sanctum: Autenticazione tramite Sanctum per le richieste API
        $middleware->api(prepend: [
            ForceJsonResponse::class,
            EnsureFrontendRequestsAreStateful::class,
        ]);

        $middleware->api(append: [
            'throttle:api',
            'auth:sanctum',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {

        $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e) {
            return $request->is('api/*');
        });

        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(['message' => 'Resource not found.'], 404);
            }
        });

        // 1. Render Unauthenticated in JSON per API
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
        });

        // 2. Render Method Not Allowed in JSON per API
        $exceptions->render(function (MethodNotAllowedHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(['message' => 'Method Not Allowed.'], 405);
            }
        });

        // 3. Render ValidationException in JSON per API
        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(['message' => 'Validation failed.', 'errors' => $e->validator->errors()], 422);
            }
        });

        // 4. render all other exceptions in JSON per API
        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(['message' => 'Retry later.'], 500);
            }
        });

    })->create();
