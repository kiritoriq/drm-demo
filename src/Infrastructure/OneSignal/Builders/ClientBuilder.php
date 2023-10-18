<?php

namespace Infrastructure\OneSignal\Builders;

use Illuminate\Http\Client\PendingRequest;
use Infrastructure\OneSignal\Actions\ResolveClientBuilder;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class ClientBuilder extends Action
{
    protected readonly null | string $token;

    protected readonly string $baseUrl;

    public function getToken(): null | string
    {
        return $this->token;
    }

    public function setToken(null | string $token): static
    {
        $this->token = $token;

        return $this;
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function setBaseUrl(string $baseUrl): static
    {
        $this->baseUrl = $baseUrl;

        return $this;
    }

    public function toFactory(): PendingRequest
    {
        return ResolveClientBuilder::resolve()->execute($this);
    }
}
