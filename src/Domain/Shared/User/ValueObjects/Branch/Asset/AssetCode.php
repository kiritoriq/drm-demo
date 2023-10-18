<?php

namespace Domain\Shared\User\ValueObjects\Branch\Asset;

use App\Exceptions\InvalidGivenDataException;

final class AssetCode
{
    public readonly string | null $formatted;

    /**
     * @throws Throwable
     */
    public function __construct(
        public readonly int $latestAssetCode,
        public readonly int $branchId,
        public readonly int | null $categoryId
    ) {
        throw_if(
            condition: $latestAssetCode == 0,
            exception: new InvalidGivenDataException(message: 'Latest Branch Asset id should greather than 1')
        );

        $this->formatted = $this->resolveNumberWithPrefix();
    }

    /**
     * @throws Throwable
     */
    public static function from(int $latestAssetCode): AssetCode
    {
        return new AssetCode($latestAssetCode);
    }

    protected function resolveNumberWithPrefix(): string
    {
        $prefix = 'A' . $this->branchId;

        $infix = str_pad($this->categoryId, 2, '0', STR_PAD_LEFT);

        $suffix = str_pad(
            string: (string) $this->latestAssetCode,
            length: 4,
            pad_string: '0',
            pad_type: STR_PAD_LEFT
        );

        return $prefix . '-' . $infix . '-' . $suffix;
    }
}
