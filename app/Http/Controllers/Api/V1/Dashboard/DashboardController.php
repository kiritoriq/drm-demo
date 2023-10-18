<?php

namespace App\Http\Controllers\Api\V1\Dashboard;

use App\Http\Controllers\Controller;
use Domain\Task\Actions\FetchTaskStatisticAction;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $data = FetchTaskStatisticAction::resolve()->execute(user: $request->user());

        return response()->json([
            'success' => true,
            'data' => $data,
        ], 200);
    }
}
