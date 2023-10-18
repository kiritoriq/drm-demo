<?php

namespace Domain\Shared\Foundation\Concerns\Enum;

use BackedEnum;
use Domain\Shared\Foundation\Support\Str;
use UnitEnum;

trait HasCaseResolver
{
    public static function getCases(): array
    {
        $cases = [];

        foreach (self::cases() as $case) {
            $cases[] = static::resolveValue(enum: $case);
        }

        return $cases;
    }

    public static function getCaseOptions(): array
    {
        $cases = [];

        foreach (self::cases() as $case) {
            $cases[$case->value] = static::resolveValue(enum: $case);
        }

        return $cases;
    }

    public static function resolveValue(UnitEnum | BackedEnum $enum)
    {
        return $enum instanceof BackedEnum
            ? Str::headline($enum->name)
            : $enum->value;
    }
}
