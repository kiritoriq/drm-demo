<?php

namespace App\DataTransferObjects\Task;

use KoalaFacade\DiamondConsole\Foundation\DataTransferObject;

readonly class ReadLogData extends DataTransferObject
{
    public function __construct(
        public readonly int | string $logId
    ) {
        // 
    }
}