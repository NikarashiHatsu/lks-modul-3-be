<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use App\Models\Follow;
use App\Models\User;
use App\Services\UserService;
use App\Services\FollowService;
use Illuminate\Http\Request;

class UserDetailController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, User $user)
    {
        $user->load([
            'posts',
        ]);

        $user->loadCount([
            'posts',
            'followers',
            'followings',
        ]);

        $user['is_your_account'] = UserService::isOwnAccount($request->user(), $user);

        $user['following_status'] = UserService::getFollowingStatus($request->user(), $user);

        if (UserService::isPrivate($user) || UserService::isStatusNotFollowingOrRequested($user['following_status'])) {
            unset($user->posts);
        }

        return response()->json($user);
    }
}
