<?php

namespace App\DataTransferObjects\Task;

use KoalaFacade\DiamondConsole\Foundation\DataTransferObject;

readonly class RejectData extends DataTransferObject
{
    public function __construct(
        public readonly string $rejectReason,
        public readonly string | null $description
    )
    {
    }
}
