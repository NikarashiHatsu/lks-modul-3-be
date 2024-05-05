<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Services\FollowService;
use Illuminate\Http\Request;

class FollowingController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        return response()->json([
            'following' => FollowService::following($request->user()),
        ]);
    }
}
