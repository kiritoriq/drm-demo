<?php

namespace App\Http\Controllers\Api\V1\Review\Contractor;

use App\DataTransferObjects\Review\SearchData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Review\SearchRequest;
use App\Http\Resources\Review\Collection;
use Domain\Review\Actions\Contractor\FetchReviewsAction;
use Illuminate\Contracts\Support\Responsable;

class ReviewController extends Controller
{
    public function index(SearchRequest $request): Responsable
    {
        return new Collection(
            FetchReviewsAction::resolve()
                ->execute(
                    data: SearchData::resolveFrom($request->has('stars') ? $request->validated() : ['stars' => null]),
                    user: $request->user()
                )
                ->paginate()
        );
    }
}
