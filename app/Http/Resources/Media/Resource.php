<?php

namespace App\Http\Resources\Media;

use Domain\Shared\Foundation\Resources\JsonResource;
use Illuminate\Http\Request;

class Resource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'original_url' => $this->getUrl(),
            'thumb_url' => $this->getUrl('thumb')
        ];
    }
}
