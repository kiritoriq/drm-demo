<?php

namespace Domain\Shared\Version\Builders;

use Illuminate\Database\Eloquent\Builder;

class VersionBuilder extends Builder
{
    public function applicationType(string $type): static
    {
        return $this->where(column: 'application_type', operator: '=', value: $type);
    }

    public function active(): static
    {
        return $this->where(column: 'status', operator: '=', value: true);
    }

    public function shouldForceUpdateAgainst(int $major, int $minor, int $patch): bool
    {
        if ($this->model->major_version > $major) {
            return true;
        }

        if ($this->model->major_version < $major) {
            return false;
        }

        if ($this->model->minor_version > $minor) {
            return true;
        }

        return false;
    }
}