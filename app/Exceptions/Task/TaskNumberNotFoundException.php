<?php

namespace App\Exceptions\Task;

use App\Exceptions\RenderToJson;
use Illuminate\Http\Response;

class TaskNumberNotFoundException extends \Exception
{
    use RenderToJson;

    public function __construct()
    {
        parent::__construct(
            message: __('The task number you entered is not found!'),
            code: Response::HTTP_NOT_FOUND
        );
    }
}
