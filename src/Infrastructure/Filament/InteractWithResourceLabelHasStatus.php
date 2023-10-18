<?php

namespace Infrastructure\Filament;

use Filament\Resources\Resource;

/**
 * @mixin Resource
 */
trait InteractWithResourceLabelHasStatus
{
    /**
     * @return string
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public static function getPluralModelLabel(): string
    {
        return self::$label . ' ' . (request()->get(key: 'status', default: null) == 'quote_requested' ? 'Pending' : request()->get(key: 'status', default: null));
    }
}
