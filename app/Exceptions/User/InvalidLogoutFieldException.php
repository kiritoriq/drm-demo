<?php

namespace App\Exceptions\User;

use App\Exceptions\RenderToJson;
use Illuminate\Http\Response;

class InvalidLogoutFieldException extends \Exception
{
    use RenderToJson;

    public function __construct()
    {
        parent::__construct(
            message: __('Player Id not sent!'),
            code: Response::HTTP_BAD_REQUEST
        );
    }
}
