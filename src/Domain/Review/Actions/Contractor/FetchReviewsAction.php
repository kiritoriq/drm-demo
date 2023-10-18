<?php

namespace Domain\Review\Actions\Contractor;

use App\DataTransferObjects\Review\SearchData;
use Domain\Review\Builders\ReviewBuilder;
use Domain\Shared\Review\Models\Review;
use Domain\Shared\User\Models\User;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class FetchReviewsAction extends Action
{
    public function execute(SearchData $data, User $user): ReviewBuilder
    {
        return Review::query()
            ->whereAssignedToContractor($user)
            ->when(
                value: filled ($data->stars),
                callback: fn (ReviewBuilder $query) => $query->where('stars', $data->stars)
            );
    }
}
