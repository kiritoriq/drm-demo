<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\DataTransferObjects\User\ForgotPasswordData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Responses\HttpResponse;
use Domain\User\Actions\ForgotPasswordAction;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Response;

class ForgotPasswordController extends Controller
{
    public function __invoke(ForgotPasswordRequest $request): Responsable
    {
        ForgotPasswordAction::resolve()
            ->execute(
                data: ForgotPasswordData::resolve($request->validated())
            );

        return new HttpResponse(
            success: true,
            code: Response::HTTP_OK,
            message: 'Reset password link has been sent to your email'
        );
    }
}
