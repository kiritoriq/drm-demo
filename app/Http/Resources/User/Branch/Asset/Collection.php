<?php

namespace App\Http\Resources\User\Branch\Asset;

use Domain\Shared\Foundation\Resources\ResourceCollection;
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
}
