<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\FollowService;

class FollowerController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(User $user)
    {
        return response()->json([
            'followers' => FollowService::followers($user),
        ]);
    }
}
