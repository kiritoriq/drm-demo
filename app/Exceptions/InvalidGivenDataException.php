<?php

namespace App\Exceptions;

use Illuminate\Http\Response;

class InvalidGivenDataException extends \Exception
{
    public function __construct(string $message)
    {
        parent::__construct(
            message: $message,
            code: Response::HTTP_BAD_REQUEST
        );
    }
}
