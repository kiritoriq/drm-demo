<?php

namespace Domain\Shared\Foundation;

use Illuminate\Support\Facades\Log;

class GetBuildNumber
{
    public static function execute()
    {
        try {
            return file_get_contents(base_path() . '/.build_number');
        } catch (\Exception $e) {
            Log::info($e->getMessage());

            return false;
        }
    }
}