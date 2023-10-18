<?php

namespace App\Exceptions\User;

use App\Exceptions\RenderToJson;
use Illuminate\Http\Response;

class ExactlySamePasswordException extends \Exception
{
    use RenderToJson;

    public function __construct()
    {
        parent::__construct(
            message: __('Your new password cannot be the same as your current password.'),
            code: Response::HTTP_FORBIDDEN
        );
    }
}
