<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\FollowService;
use Illuminate\Http\Request;

class AcceptController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, User $user)
    {
        if (FollowService::isUserNotFollowing($request->user(), $user)) {
            return response()->json([
                'message' => 'The user is not following you',
            ], 422);
        }

        if (FollowService::isFollowRequestAlreadyAccepted($request->user(), $user)) {
            return response()->json([
                'message' => 'Follow request is already accepted',
            ], 422);
        }

        try {
            FollowService::accept($request->user(), $user);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Accepting follow request failed: ' . $th->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => 'Follow request accepted',
        ]);
    }
}
