<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\FollowService;
use Illuminate\Http\Request;

class UnfollowController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, User $user)
    {
        if (empty(FollowService::isAlreadyFollowing($request->user(), $user))) {
            return response()->json([
                'message' => 'You are not following the user',
            ], 422);
        }

        try {
            FollowService::unfollow($request->user(), $user);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Following user failed: ' . $th->getMessage(),
            ], 500);
        }

        return response()->noContent();
    }
}
