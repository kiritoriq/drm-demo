<?php

namespace App\Http\Controllers\Api\V1\User;

use App\DataTransferObjects\User\UpdatePasswordData;
use App\DataTransferObjects\User\UpdateProfileData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UpdatePasswordRequest;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Http\Requests\User\UploadProfilePictureRequest;
use App\Http\Resources\User\Contractor\Resource;
use App\Http\Responses\HttpResponse;
use Domain\User\Actions\DeactivateAccountAction;
use Domain\User\Actions\UpdatePasswordAction;
use Domain\User\Actions\UpdateProfileAction;
use Domain\User\Actions\UploadProfilePictureAction;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProfileController extends Controller
{
    public function show(Request $request): Responsable
    {
        return new Resource(
            resource: $request->user()
        );
    }

    public function update(UpdateProfileRequest $request): Responsable
    {
        $updated = UpdateProfileAction::resolve()
            ->execute(
                data: UpdateProfileData::resolve($request->validated()),
                user: $request->user()
            );

        return new Resource(
            resource: $updated
        );
    }

    public function updatePassword(UpdatePasswordRequest $request): Responsable
    {
        $updated = UpdatePasswordAction::resolve()
            ->execute(
                data: UpdatePasswordData::resolve($request->except('password_confirmation')),
                user: $request->user()
            );

        return new Resource(
            resource: $updated
        );
    }

    public function uploadProfilePicture(UploadProfilePictureRequest $request): Responsable
    {
        $uploaded = UploadProfilePictureAction::resolve()
            ->execute(
                user: $request->user(),
                data: $request->validated()
            );

        return new Resource(
            resource: $uploaded
        );
    }

    public function deactivate(Request $request): Responsable
    {
        $deactivated = DeactivateAccountAction::resolve()
            ->execute(
                user: $request->user()
            );
        
        if ($deactivated) {
            return new HttpResponse(
                success: true,
                code: Response::HTTP_OK,
                message: 'Deactivate account success.'
            );
        }

        return new HttpResponse(
            success: false,
            code: Response::HTTP_INTERNAL_SERVER_ERROR,
            message: 'Deactivate account failed'
        );
    }
}
