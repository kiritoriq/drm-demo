<?php

namespace App\Exceptions\User;

use App\Exceptions\RenderToJson;
use Exception;
use Illuminate\Http\Response;

class WrongPasswordException extends Exception
{
    use RenderToJson;

    public function __construct()
    {
        parent::__construct(
            message: __('Your password is incorrect or this account doesn\'t exist.'),
            code: Response::HTTP_FORBIDDEN
        );
    }
}
