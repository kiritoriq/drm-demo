<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

/**
 * @mixin Exception
 */
trait RenderToJson
{
    /**
     * Render the exception into an HTTP response.
     */
    public function render(): JsonResponse
    {
        return response()->json(
            data: [
                'success' => false,
                'message' => $this->getMessage(),
            ],
            status: $this->getCode()
        );
    }
}
