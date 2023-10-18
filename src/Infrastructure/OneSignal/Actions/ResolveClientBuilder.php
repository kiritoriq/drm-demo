<?php

namespace Infrastructure\OneSignal\Actions;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Infrastructure\OneSignal\Builders\ClientBuilder;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class ResolveClientBuilder extends Action
{
    public function execute(ClientBuilder $builder): PendingRequest
    {
        return Http::baseUrl($builder->getBaseUrl())
            ->when(
                value: $builder->getToken(),
                callback: fn (PendingRequest $request) => $request
                    ->withToken(token: $builder->getToken(), type: 'Basic')
            );
    }
}
