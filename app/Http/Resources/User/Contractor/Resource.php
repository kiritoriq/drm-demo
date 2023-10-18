<?php

namespace App\Http\Resources\User\Contractor;

use App\Http\Resources\Service\Resource as ServiceResource;
use App\Http\Resources\State\Resource as StateResource;
use Domain\Shared\Foundation\Resources\JsonResource;
use Domain\Shared\User\Models\User;
use Illuminate\Http\Request;

/**
 * @mixin  User
 */
class Resource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'contact_number' => $this->phone,
            'whatsapp_number' => $this->whatsapp_number,
            'vendor_type' => $this->vendor_type,
            'profile_picture' => [
                'original_image' => $this->getFirstMediaUrl(User::COLLECTION_NAME),
                'thumb_image' => $this->getFirstMediaUrl(User::COLLECTION_NAME, User::MEDIA_CONVERSION_NAME)
            ],
            'business_name' => $this->company_name,
            'business_description' => $this->business_description,
            'business_address' => $this->business_address,
            'business_logo' => [
                'original_url' => $this->getFirstMediaUrl(User::BUSINESS_MEDIA_COLLECTION_NAME),
                'thumb' => $this->getFirstMediaUrl(User::BUSINESS_MEDIA_COLLECTION_NAME, User::BUSINESS_MEDIA_CONVERSION_NAME),
            ],
            'offered_services' => ServiceResource::collection($this->offeredServices),
            'location_coverages' => StateResource::collection($this->locationCoverages),
            'is_verified' => $this->isVerified(),
            'meta' => [
                'total_reviews_count' => $this->reviews()->count(),
                'total_reviews_stars' => number_format(floatval($this->reviews()->avg('stars')), 2, '.', ''),
                'total_wallet_amount' => floatval(number_format($this->transactionHistories()->sum('amount'), 2, '.', ''))
            ],
        ];
    }

    public function with($request): array
    {
        return [
            'success' => true,
            'message' => 'Your application is submitted to verification'
        ];
    }
}
