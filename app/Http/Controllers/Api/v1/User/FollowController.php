<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\FollowService;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, User $user)
    {
        if (FollowService::isFollowingSelf($request->user(), $user)) {
            return response()->json([
                'message' => 'You are not allowed to follow yourself',
            ], 422);
        }

        if (! empty($userFollowed = FollowService::isAlreadyFollowing($request->user(), $user))) {
            return response()->json([
                'message' => 'You are already followed',
                'status' => $userFollowed->is_accepted
                    ? 'following'
                    : 'requested',
            ], 422);
        }

        try {
            $follow = FollowService::follow($request->user(), $user);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Following user failed: ' . $th->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => 'Follow success',
            'status' => $follow->is_accepted
                ? 'following'
                : 'requested',
        ], 201);
    }
}
