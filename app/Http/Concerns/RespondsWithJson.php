<?php

namespace App\Http\Concerns;

use Illuminate\Http\JsonResponse;

trait RespondsWithJson
{
    protected function success(array $data = [], int $status = 200): JsonResponse
    {
        return response()->json($data, $status);
    }

    protected function created(array $data = []): JsonResponse
    {
        return response()->json($data, 201);
    }

    protected function noContent(): JsonResponse
    {
        return response()->json([], 204);
    }

    protected function error(string $message, int $status = 400, array $errors = []): JsonResponse
    {
        $payload = ['message' => $message];

        if ($errors !== []) {
            $payload['errors'] = $errors;
        }

        return response()->json($payload, $status);
    }

    protected function unauthorized(string $message = 'Unauthorized.'): JsonResponse
    {
        return $this->error($message, 401);
    }

    protected function forbidden(string $message = 'Forbidden.'): JsonResponse
    {
        return $this->error($message, 403);
    }

    protected function notFound(string $message = 'Resource not found.'): JsonResponse
    {
        return $this->error($message, 404);
    }

    protected function unprocessable(string $message = 'Validation failed.', array $errors = []): JsonResponse
    {
        return $this->error($message, 422, $errors);
    }

    protected function serverError(string $message = 'Server error.'): JsonResponse
    {
        return $this->error($message, 500);
    }
}
