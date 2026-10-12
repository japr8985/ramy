<?php

namespace App\Shared\Infrastructure\Traits;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
trait ApiResponse{
    /**
     * Respuesta exitosa estandarizada (200, 201, etc.).
     */
    protected function success(
        mixed $data = null,
        string $message = 'Operación exitosa.',
        int $statusCode = Response::HTTP_OK
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'status'  => $statusCode,
            'message' => $message,
            'data'    => $data,
            'errors'  => null,
        ], $statusCode);
    }

    /**
     * Respuesta de error estandarizada (400, 401, 403, 404, 500, etc.).
     */
    protected function error(
        string $message = 'Ha ocurrido un error.',
        int $statusCode = Response::HTTP_BAD_REQUEST,
        mixed $errors = null
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'status'  => $statusCode,
            'message' => $message,
            'data'    => null,
            'errors'  => $errors,
        ], $statusCode);
    }

    /**
     * Atajo para recursos creados (201).
     */
    protected function created(
        mixed $data = null,
        string $message = 'Recurso creado correctamente.'
    ): JsonResponse {
        return $this->success($data, $message, Response::HTTP_CREATED);
    }

    /**
     * Atajo para no autorizado (401).
     */
    protected function unauthorized(
        string $message = 'No autorizado.'
    ): JsonResponse {
        return $this->error($message, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Atajo para errores de validación de formulario / DTO (422).
     */
    protected function validationError(
        mixed $errors,
        string $message = 'Los datos proporcionados no son válidos.'
    ): JsonResponse {
        return $this->error($message, Response::HTTP_UNPROCESSABLE_ENTITY, $errors);
    }
}