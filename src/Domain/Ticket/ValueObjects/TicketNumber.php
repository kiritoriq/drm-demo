<?php

namespace Domain\Ticket\ValueObjects;

use App\Exceptions\InvalidGivenDataException;
use Carbon\Carbon;
use Domain\Shared\Ticket\Models\Project;

final class TicketNumber
{
    public readonly string | null $formatted;

    /**
     * @throws Throwable
     */
    public function __construct(
        public readonly int | string $latestNumber,
        public readonly Carbon $date,
        public readonly Project | null $project,
    ) {
        throw_if(
            condition: $latestNumber == 0,
            exception: new InvalidGivenDataException(message: 'Latest Ticket id should greather than 1')
        );

        $this->formatted = $this->resolveNumberWithPrefix();
    }

    /**
     * @throws Throwable
     */
    public static function from(int $latestNumber, Carbon $date, Project | null $project): TicketNumber
    {
        return new TicketNumber($latestNumber, $date, $project);
    }

    protected function resolveNumberWithPrefix(): string
    {
        $prefix = filled($this->project) ? $this->project->name : '';

        $infix = date('y', strtotime($this->date)) . date('m', strtotime($this->date));

        $suffix = str_pad(
            string: (string) $this->latestNumber,
            length: 4,
            pad_string: '0',
            pad_type: STR_PAD_LEFT
        );

        return $prefix . $infix . '-' . $suffix;
    }
}
