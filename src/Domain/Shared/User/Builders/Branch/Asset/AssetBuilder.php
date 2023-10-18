<?php

namespace Domain\Shared\User\Builders\Branch\Asset;

use Illuminate\Database\Eloquent\Builder;

class AssetBuilder extends Builder
{
    public function isPreventiveCycleIsMonth(): bool
    {
        return $this->model->preventive_cycle === 'month';
    }

    public function isPreventiveCycleIsYear(): bool
    {
        return $this->model->preventive_cycle === 'year';
    }
}