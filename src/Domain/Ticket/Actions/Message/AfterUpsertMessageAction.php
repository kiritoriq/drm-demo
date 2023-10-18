<?php

namespace Domain\Ticket\Actions\Message;

use Domain\Shared\Ticket\Models\Message;
use Domain\Ticket\Events\Message\MessageSent;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class AfterUpsertMessageAction extends Action
{
    public function execute(Message $message): void
    {
        MessageSent::dispatch(
            $message,
            $message->ticket->customer->email,
            new \Domain\Ticket\Mails\Message\MessageSent($message)
        );
    }
}
