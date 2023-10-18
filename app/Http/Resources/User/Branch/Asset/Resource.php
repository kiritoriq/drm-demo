<?php

namespace App\Http\Resources\User\Branch\Asset;

use App\Http\Resources\Area\Resource as AreaResource;
use App\Http\Resources\User\Resource as UserResource;
use App\Http\Resources\User\Branch\Resource as BranchResource;
use Domain\Shared\Foundation\Resources\JsonResource;
use Domain\Shared\User\Models\BranchAsset;
use Illuminate\Http\Request;

/**
 * @mixin BranchAsset
 */
class Resource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'asset_code' => $this->asset_code,
            'name' => $this->name,
            'description' => $this->description,
            'brand' => $this->brand,
            'model' => $this->model,
            'year_make' => $this->year_make,
            'preventive_services' => $this->preventive_services,
            'vendor_purchased_from' => $this->vendor_purchased_from,
            'warranty_expiry_date' => $this->warranty_expiry_date,
            'year_purchased' => $this->year_purchased,
            'area' => new AreaResource($this->area),
            'asset_type' => new Type\Resource($this->assetType),
            'assignee' => new UserResource($this->assignee),
            'images' => \App\Http\Resources\Media\Resource::collection($this->getMedia(BranchAsset::IMAGE_COLLECTION_NAME)),
            'branch' => new BranchResource($this->whenLoaded('branch'))
        ];
    }
}
