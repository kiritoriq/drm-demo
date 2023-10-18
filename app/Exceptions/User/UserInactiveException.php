<?php

namespace App\Exceptions\User;

use App\Exceptions\RenderToJson;
use Illuminate\Http\Response;
use Exception;

class UserInactiveException extends Exception
{
    use RenderToJson;

    public function __construct()
    {
        parent::__construct(
            message: __('Your account already inactive.'),
            code: Response::HTTP_FORBIDDEN
        );
    }
}