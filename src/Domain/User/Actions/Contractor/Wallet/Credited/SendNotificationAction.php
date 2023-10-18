<?php

namespace Domain\User\Actions\Contractor\Wallet\Credited;

use App\DataTransferObjects\Notification\CreateNotificationData;
use Domain\Shared\User\Models\ContractorWallet;
use Domain\Task\Actions\Notifiable\ResolveExternalUserIdsAction;
use Domain\Task\Actions\SentNotificationAction;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class SendNotificationAction extends Action
{
    public function execute(ContractorWallet $wallet): void
    {
        if ($wallet->task->doesntHaveContractor()) {
            return;
        }

        ResolveExternalUserIdsAction::resolve()
            ->resolveTask($wallet->task)
            ->execute(
                data: new CreateNotificationData(
                    title: __(key: 'notification.contractor.wallet.credited.title'),
                    content: __(
                        key: 'notification.contractor.wallet.credited.content',
                        replace:[
                            'amount' => 'RM ' . number_format($wallet->amount, 2, '.'),
                            'task_number' => $wallet->task->task_number,
                        ])
                ),
                afterSending: SentNotificationAction::resolve()
            );
    }
}