<?php

namespace Domain\Shared\Foundation\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource as Resource;

class JsonResource extends Resource
{
    /**
     * Get additional data that should be returned with the resource array.
     *
     * @param  Request  $request
     */
    public function with($request): array
    {
        return ['success' => true];
    }
}
