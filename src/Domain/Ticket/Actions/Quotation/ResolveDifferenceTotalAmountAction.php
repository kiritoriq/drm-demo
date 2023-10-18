<?php

namespace Domain\Ticket\Actions\Quotation;

use Domain\Shared\Ticket\Models\Quotation;
use Domain\Ticket\Tappable\Quotation\FilterForOwnUser;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class ResolveDifferenceTotalAmountAction extends Action
{
    public function execute(): array
    {
        return ResolvePercentageAmountDifferenceAction::resolve()
            ->execute(
                query: Quotation::query()
                    ->tap(new FilterForOwnUser(attribute: 'raised_by_id'))
            );
    }
}