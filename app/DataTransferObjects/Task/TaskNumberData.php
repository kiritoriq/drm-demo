<?php

namespace App\DataTransferObjects\Task;

use KoalaFacade\DiamondConsole\Foundation\DataTransferObject;

readonly class TaskNumberData extends DataTransferObject
{
    public function __construct(
        public readonly string $taskNumber
    )
    {
    }
}
