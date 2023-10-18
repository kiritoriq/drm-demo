<?php

namespace Domain\Shared\Version\Enums;

use Domain\Shared\Foundation\Concerns\Enum\HasCaseResolver;

enum ApplicationType: string
{
    use HasCaseResolver;

    case Android = 'android';

    case Ios = 'ios';

    case Huawei = 'huawei';
}