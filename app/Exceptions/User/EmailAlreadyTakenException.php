<?php

namespace App\Exceptions\User;

use App\Exceptions\RenderToJson;
use Illuminate\Http\Response;

class EmailAlreadyTakenException extends \Exception
{
    use RenderToJson;

    public function __construct()
    {
        parent::__construct(
            message: __('Email address is already used!'),
            code: Response::HTTP_UNPROCESSABLE_ENTITY
        );
    }
}