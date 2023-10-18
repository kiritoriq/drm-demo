<?php

namespace App\Exceptions\Task;

use App\Exceptions\RenderToJson;
use Illuminate\Http\Response;

class InvalidStatusException extends \Exception
{
    use RenderToJson;

    public function __construct()
    {
        parent::__construct(
            message: __('The status given is invalid'),
            code: Response::HTTP_BAD_REQUEST
        );
    }
}
