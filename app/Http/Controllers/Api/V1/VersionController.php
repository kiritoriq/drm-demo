<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Domain\Shared\Version\Enums\ApplicationType;
use Domain\Shared\Version\Models\Version;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VersionController extends Controller
{
    public function checkMobileVersion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'application_type' => 'required|string|in:' . implode(',', [
                ApplicationType::Android->value,
                ApplicationType::Ios->value,
                ApplicationType::Huawei->value,
            ]),
            'version' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ]);
        }

        $separatedVersion = explode('.', $request->version);
        $major = $separatedVersion[0];
        $minor = $separatedVersion[1];
        $patch = $separatedVersion[2];

        $latest = Version::query()
            ->applicationType($request['application_type'])
            ->active()
            ->orderBy('major_version', 'desc')
            ->orderBy('minor_version', 'desc')
            ->orderBy('patch_version', 'desc')
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => [
                'application_type' => $request['application_type'],
                'your_version' => $major . '.' . $minor . '.' . $patch,
                'latest_version' => $latest->major_version . '.' . $latest->minor_version . '.' . $latest->patch_version,
                'should_force_update' => $latest->shouldForceUpdateAgainst((int) $major, (int) $minor, (int) $patch),
            ],
        ], 200);
    }
}