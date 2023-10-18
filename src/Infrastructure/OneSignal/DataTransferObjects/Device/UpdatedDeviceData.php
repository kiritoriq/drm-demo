<?php

namespace Infrastructure\OneSignal\DataTransferObjects\Device;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use KoalaFacade\DiamondConsole\Foundation\DataTransferObject;

readonly class UpdatedDeviceData extends DataTransferObject
{
    public function __construct(
        public readonly bool $success
    ) {  
    }

    public static function resolveFromResponse(Response $response): static
    {
        return static::resolveFromArray($response->json());
    }

    public static function resolveFromArray(array $data): static
    {
        return new static(
            success: Arr::get($data, key: 'success', default: false),
        );
    }
}