<?php

namespace Domain\Shared\Foundation\Support;

class Str extends \Illuminate\Support\Str
{
    public static function descriptionText(string $description): string
    {
        $modified = substr($description, 0, 50);

        if (strlen($modified) < strlen($description)) {
            return $modified . '...';
        }

        return $modified;
    }
}
