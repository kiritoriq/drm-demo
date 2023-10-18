<?php

namespace App\Exceptions\Notification;

use App\Exceptions\RenderToJson;
use Illuminate\Http\Response;

class NotFoundException extends \Exception
{
    use RenderToJson;

    public function __construct()
    {
        parent::__construct(
            message: __('The notification you looking for is not found!'),
            code: Response::HTTP_NOT_FOUND
        );
    }
}
