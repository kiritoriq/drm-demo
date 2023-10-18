<?php

namespace Infrastructure\Laravel;

use Illuminate\Pagination\LengthAwarePaginator as Pagination;

class LengthAwarePaginator extends Pagination
{
    public function toArray()
    {
        return [
            'data' => $this->items->toArray(),
            'links' => [
                'first' => $this->url(1),
                'last' => $this->url($this->lastPage()),
                'prev' => $this->previousPageUrl(),
                'next' => $this->nextPageUrl()
            ],
            'meta' => [
                'current_page' => $this->currentPage(),
                'from' => $this->firstItem(),
                'last_page' => $this->lastPage(),
                'links' => $this->linkCollection()->toArray(),
                'path' => $this->path(),
                'per_page' => $this->perPage(),
                'to' => $this->lastItem(),
                'total' => $this->total(),
                
            ],
            'success' => true
        ];
    }
}