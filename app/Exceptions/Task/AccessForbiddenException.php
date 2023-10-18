<?php

namespace App\Exceptions\Task;

use App\Exceptions\RenderToJson;
use Illuminate\Http\Response;

class AccessForbiddenException extends \Exception
{
    use RenderToJson;

    public function __construct()
    {
        parent::__construct(
            message: __('Can not access this Task! This task is belongs to others user'),
            code: Response::HTTP_FORBIDDEN
        );
    }
}
