<?php

namespace App\Http\Resources\Wallet;

use App\Http\Resources\User\Contractor\Resource as ContractorResource;
use App\Http\Resources\Task\Resource as TaskResource;
use Domain\Shared\Foundation\Resources\JsonResource;
use Domain\Shared\User\Models\ContractorWallet;
use Illuminate\Http\Request;

/**
 * @mixin ContractorWallet
 */
class Resource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'amount' => floatval($this->amount),
            'is_redeemed' => ($this->is_redeemed == 1 ? true : false),
            'redeemed_at' => $this->redeemed_at,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'contractor' => new ContractorResource($this->whenLoaded('contractor')),
            'task' => new TaskResource($this->task),
        ];
    }
}
