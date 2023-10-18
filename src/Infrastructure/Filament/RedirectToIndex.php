<?php

namespace Infrastructure\Filament;

trait RedirectToIndex
{
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
