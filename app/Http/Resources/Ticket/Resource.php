<?php

namespace App\Http\Resources\Ticket;

use App\Http\Resources\Area\Resource as AreaResource;
use App\Http\Resources\Media\Resource as MediaResource;
use App\Http\Resources\User\Resource as UserResource;
use App\Http\Resources\User\Branch\Resource as BranchResource;
use App\Http\Resources\User\Branch\Asset\Resource as AssetResource;
use Domain\Shared\Foundation\Resources\JsonResource;
use Domain\Shared\Ticket\Models\Ticket;
use Illuminate\Http\Request;

/**
 *@mixin Ticket
 */
class Resource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ticket_number' => $this->ticket_number,
            'subject' => $this->subject,
            'description' => $this->description,
            'due_at' => $this->due_at,
            'created_at' => $this->created_at->format('Y-m-d'),
            'last_update_at' => $this->updated_at->format('Y-m-d'),
            'images' => MediaResource::collection($this->getMedia(Ticket::COLLECTION_NAME)),
            'project' => new Project\Resource($this->project),
            'assignee' => new UserResource($this->whenLoaded('assignee')),
            'customer' => new UserResource($this->customer),
            'branch' => new BranchResource($this->branch),
            'areas' => AreaResource::collection($this->areas),
        ];
    }
}
