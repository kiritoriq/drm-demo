<?php

namespace Domain\Shared\Foundation;

use Illuminate\Container\Container;

abstract class Action
{
    public function __construct(protected Container $container)
    {
        //
    }

    public static function resolve(): static
    {
        return app(static::class);
    }
}
