<?php

namespace App\Exceptions\User;

use App\Exceptions\RenderToJson;
use Illuminate\Http\Response;

class UnmatchPasswordException extends \Exception
{
    use RenderToJson;

    public function __construct()
    {
        parent::__construct(
            message: __('The old password you entered does not match your current password.'),
            code: Response::HTTP_FORBIDDEN
        );
    }
}
