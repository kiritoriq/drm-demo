<?php

namespace App\DataTransferObjects\Review;

use Domain\Shared\User\Models\User;
use KoalaFacade\DiamondConsole\Foundation\DataTransferObject;

readonly class SearchData extends DataTransferObject
{
    public function __construct(
        public readonly null | string | int $stars,
    )
    {
    }
}
