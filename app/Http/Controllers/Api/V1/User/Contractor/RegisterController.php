<?php

namespace App\Http\Controllers\Api\V1\User\Contractor;

use App\DataTransferObjects\User\RegisterData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\User\Contractor\Resource;
use Domain\Shared\User\Models\User;
use Domain\User\Actions\Contractor\RegisterAction;
use Illuminate\Contracts\Support\Responsable;

class RegisterController extends Controller
{
    public function __invoke(RegisterRequest $request): Responsable
    {
        $registered = RegisterAction::resolve()
            ->execute(
                contactInfoData: RegisterData::resolve($request->except(['password_confirmation', 'business_logo', 'services', 'location_coverages'])),
                services: $request->services,
                locationCoverages: $request->location_coverages
            );

        if ($request->has('business_logo')) {
            $registered->addMediaFromRequest('business_logo')->toMediaCollection(User::BUSINESS_MEDIA_COLLECTION_NAME);
        }

        return new Resource(
            resource: $registered
        );
    }
}
