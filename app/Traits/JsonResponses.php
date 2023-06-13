<?php

namespace App\Traits;

use App\Enums\ErrorCodes;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\JsonResponse;
use JsonSerializable;
use Stringable;
use Throwable;

trait JsonResponses
{
    public function sendResponse(
        string|array|Stringable|Arrayable|JsonSerializable|null $data = [],
        int $statusCode = 200,
        string $message = 'Success',
        array $headers = []
    ): JsonResponse {
        return $this->response(
            $statusCode,
            $data,
            $message,
            headers: $headers
        );
    }

    public function sendError(
        string|Throwable $message = 'Technical Error. Please try again later',
        int $statusCode = 500,
        string|array|Stringable|Arrayable|JsonSerializable|null $errors = [],
        ?ErrorCodes $errorCode = null,
        array $headers = []

    ): JsonResponse {

        if ($message instanceof Throwable) {
            // If it is an instance of throwable, we will throw the exception.
            // Our exception handler will then convert the exception to message, statusCode, etc
            // and call this function again.
            throw $message;
        }

        return $this->response(
            $statusCode,
            message: $message,
            errors: $errors,
            errorCode: $errorCode,
            headers: $headers
        );
    }

    private function response(
        int $statusCode = 200,
        string|array|Stringable|Arrayable|JsonSerializable|null $data = [],
        string $message = 'Success',
        string|array|Stringable|Arrayable|JsonSerializable|null $errors = [],
        ?ErrorCodes $errorCode = null,
        array $headers = []
    ): JsonResponse {
        $data = $this->convertType($data);
        $errors = $this->convertType($errors, []);

        return response()->json(
            [
                'data' => $data,
                'message' => $message,
                'errors' => $errors,
                'error_code' => $errorCode,
            ],
            $statusCode
        )
            ->withHeaders($headers);
    }

    private function convertType(
        string|array|Stringable|Arrayable|JsonSerializable|null $data,
        string|array|JsonSerializable|null $default = null
    ): string|array|JsonSerializable|null {
        if ($data instanceof Stringable) {
            return $data->__toString();
        } elseif ($data instanceof Arrayable) {
            return $data->toArray();
        }
        if ($data === null && $default !== null) {
            return $default;
        }
        return $data;
    }
}
