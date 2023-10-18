<?php

namespace App\Http\Resources\Notification;

use App\Http\Resources\User\Contractor\Resource as ContractorResource;
use Domain\Shared\Foundation\Resources\JsonResource;
use Domain\Shared\User\Models\User;

class Resource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'image' => $this->image ?? $this->user?->getFirstMediaUrl(User::COLLECTION_NAME),
            'payload' => $this->payload,
            'role_id' => $this->role_id,
            'unless_role_id' => $this->unless_role_id,
            'read' => new Read\Resource($this->whenLoaded(relationship: 'read')),
            'user' => new ContractorResource($this->whenLoaded(relationship: 'user')),
            'created_at' => [
                'date' => $this->created_at->format('Y-m-d'),
                'time' => $this->created_at->format('H:i:s'),
            ],
        ];
    }
}