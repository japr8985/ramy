<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        // Asegura formato JSON para peticiones API
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        // 1. Errores de Validación (422)
        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'status'  => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'message' => 'Los datos proporcionados no son válidos.',
                    'data'    => null,
                    'errors'  => $e->errors(),
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        });

        // 2. Errores de Autenticación / Token no válido o ausente (401)
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'status'  => Response::HTTP_UNAUTHORIZED,
                    'message' => 'No autenticado o token inválido.',
                    'data'    => null,
                    'errors'  => null,
                ], Response::HTTP_UNAUTHORIZED);
            }
        });

        // 3. Recursos o rutas no encontradas (404)
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'status'  => Response::HTTP_NOT_FOUND,
                    'message' => 'El recurso solicitado no fue encontrado.',
                    'data'    => null,
                    'errors'  => null,
                ], Response::HTTP_NOT_FOUND);
            }
        });

        // 4. Otras excepciones HTTP (403, 405, etc.)
        $exceptions->render(function (HttpExceptionInterface $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'status'  => $e->getStatusCode(),
                    'message' => $e->getMessage() ?: 'Error en la petición.',
                    'data'    => null,
                    'errors'  => null,
                ], $e->getStatusCode());
            }
        });

        // 5. Errores no controlados / Servidor (500)
        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'status'  => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => config('app.debug') ? $e->getMessage() : 'Error interno del servidor.',
                    'data'    => null,
                    'errors'  => null,
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        });
    })->create();
