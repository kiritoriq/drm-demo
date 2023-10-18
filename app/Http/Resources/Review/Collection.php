<?php

namespace App\Http\Resources\Review;

use Domain\Shared\Foundation\Resources\ResourceCollection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class Collection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param $request
     * @return AnonymousResourceCollection
     */
    public function toArray($request): AnonymousResourceCollection
    {
        return Resource::collection($this->collection);
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @param  Request  $request
     * @return array
     */
    public function with($request): array
    {
        return [
            'meta' => [
                'total_reviews_count' => $this->count(),
                'total_reviews_stars' => number_format(floatval($this->avg('stars')), 1, '.', ''),
            ],
            'success' => true,
        ];
    }
}
