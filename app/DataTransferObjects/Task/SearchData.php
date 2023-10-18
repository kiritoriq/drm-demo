<?php

namespace App\DataTransferObjects\Task;

use Domain\Shared\Foundation\Requests\FormRequest;
use Illuminate\Support\Arr;
use KoalaFacade\DiamondConsole\Foundation\DataTransferObject;

readonly class SearchData extends DataTransferObject
{
    public function __construct(
        public readonly array $status
    )
    {
    }

    public static function resolveFrom(mixed $abstract): static
    {
        return static::resolveFromArray($abstract);
    }

    public static function resolveFromArray(array $data): static
    {
        return new static(
            status: Arr::get($data, key: 'status')
        );
    }
}
