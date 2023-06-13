<?php

namespace App\Traits;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\JsonResponse;
use JsonSerializable;
use Stringable;

trait JsonResponses
{
    public function sendResponse(
        string|array|Stringable|Arrayable|JsonSerializable $data = [],
        int $statusCode = 200,
        string $message = 'Success'
    ): JsonResponse {
        return $this->response(
            $statusCode,
            $data,
            $message
        );
    }

    public function sendError(
        int $statusCode = 500,
        string $message = 'Technical Error. Please try again later',
        string|array|Stringable|Arrayable|JsonSerializable $errors = [],
        string $errorCode = '',

    ): JsonResponse {
        return $this->response($statusCode, message: $message, errors: $errors, errorCode: $errorCode);
    }

    private function response(
        int $statusCode = 200,
        string|array|Stringable|Arrayable|JsonSerializable $data = [],
        string $message = 'Success',
        string|array|Stringable|Arrayable|JsonSerializable $errors = [],
        string $errorCode = '',
    ): JsonResponse {
        return response()->json([
            'data' => $data,
            'message' => $message,
            'errors' => $errors,
            'error_code' => $errorCode,
        ], $statusCode);
    }
}
