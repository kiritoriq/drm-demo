<?php

namespace App\Exceptions\User;

use App\Exceptions\RenderToJson;
use Illuminate\Http\Response;
use Exception;

class InvalidRoleException extends Exception
{
    use RenderToJson;

    public function __construct()
    {
        parent::__construct(
            message: __('Your roles didn\'t have rights to access this Apps.'),
            code: Response::HTTP_FORBIDDEN
        );
    }
}