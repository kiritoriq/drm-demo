<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;

class HttpResponse implements Responsable
{
    public function __construct(
        public readonly bool $success,
        public readonly string $code,
        public readonly null | string $message = null,
        public readonly array $data = [],
    ) {
    }

    public function toResponse($request): JsonResponse
    {
        return response()->json(data: [
            'success' => $this->success,
            'message' => $this->message,
            'data' => $this->data,
        ], status: $this->code);
    }
}
