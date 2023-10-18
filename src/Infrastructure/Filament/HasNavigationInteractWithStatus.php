<?php

namespace Infrastructure\Filament;

use Filament\Navigation\NavigationItem;
use Filament\Resources\Resource;

/**
 * @mixin Resource
 */
trait HasNavigationInteractWithStatus
{
    public static function getNavigationItems(): array
    {
        $routeBaseName = static::getRouteBaseName();

        return [
            NavigationItem::make(static::getNavigationLabel())
                ->group(static::getNavigationGroup())
                ->icon(static::getNavigationIcon())
                ->activeIcon(static::getActiveNavigationIcon())
                ->isActiveWhen(fn () => request()->routeIs("{$routeBaseName}.*") && request()->missing(key: 'status'))
                ->badge(static::getNavigationBadge(), color: static::getNavigationBadgeColor())
                ->sort(static::getNavigationSort())
                ->url(static::getNavigationUrl())
        ];
    }
}
