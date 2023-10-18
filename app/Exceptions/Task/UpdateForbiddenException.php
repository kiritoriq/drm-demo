<?php

namespace App\Exceptions\Task;

use App\Exceptions\RenderToJson;
use Illuminate\Http\Response;

class UpdateForbiddenException extends \Exception
{
    use RenderToJson;

    public function __construct()
    {
        parent::__construct(
            message: __('This task is assigned to others user'),
            code: Response::HTTP_FORBIDDEN
        );
    }
}
