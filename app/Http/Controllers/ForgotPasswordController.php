<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\ResetPasswordRequest;
use Domain\Shared\User\Models\User;
use Domain\User\Actions\ResolveResetPasswordAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function showResetPasswordForm(Request $request, $token)
    {
        $user = User::query()->where('email', $request->email)->first();

        if (blank ($user)) {
            return view('passwords.user_not_found', ['email' => $request->email]);
        }

        return view('passwords.reset', ['token' => $token, 'user' => $user]);
    }

    public function submitResetPasswordForm(ResetPasswordRequest $request)
    {
        $status = ResolveResetPasswordAction::resolve()
            ->execute(
                data: $request->validated()
            );

        return $status === Password::PASSWORD_RESET
            ? view('passwords.reset_finish')
            : view('passwords.invalid_token')->withErrors(['token' => [__($status)]]);
    }
}
