<?php

namespace App\Exceptions\Notification;

use App\Http\Responses\HttpResponse;
use Domain\Shared\Foundation\Support\Str;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RequestException extends \Exception
{
    public function __construct(public array $data = [])
    {
        parent::__construct(
            message: Response::$statusTexts[Response::HTTP_OK],
            code: Response::HTTP_OK
        );
    }

    /**
     * Report the exception.
     *
     * @return void
     */
    public function report(): void
    {
        Log::error(
            message: Str::of(':class')
                ->replace(search: ':class', replace: static::class),
            context: $this->data
        );
    }

    public function render(): Responsable
    {
        return new HttpResponse(
            success: false,
            code: Response::HTTP_OK,
            data: $this->data,
        );
    }
}