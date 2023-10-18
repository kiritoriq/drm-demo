<?php

namespace App\Exceptions\Ticket;

use App\Exceptions\RenderToJson;
use Illuminate\Http\Response;

class TicketIdNotFoundException extends \Exception
{
    use RenderToJson;

    public function __construct()
    {
        parent::__construct(
            message: __('Ticket id you entered is not found!'),
            code: Response::HTTP_NOT_FOUND
        );
    }
}
