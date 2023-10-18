<?php

namespace App\DataTransferObjects\Task;

use KoalaFacade\DiamondConsole\Foundation\DataTransferObject;

readonly class AcquireStatusData extends DataTransferObject
{
    public function __construct(
        public readonly string $lockId,
        public readonly string $abilities
    )
    {
    }
}
