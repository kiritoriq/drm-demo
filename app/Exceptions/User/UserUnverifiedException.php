<?php

namespace App\Exceptions\User;

use App\Exceptions\RenderToJson;
use Illuminate\Http\Response;
use Exception;

class UserUnverifiedException extends Exception
{
    use RenderToJson;

    public function __construct()
    {
        parent::__construct(
            message: __('Sorry, Your account is not verified yet.'),
            code: Response::HTTP_FORBIDDEN
        );
    }
}