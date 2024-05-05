<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $loggedInUser = $request->user();

        // Get the User IDs who followed the Logged In User
        $usersFollowed = $loggedInUser
            ->followings
            ->pluck('following_id');

        // Add the Logged In User ID to the array
        $usersFollowed[] = $loggedInUser->id;

        return response()->json([
            // Get the User who haven't folowwed the Logged In User
            'users' => User::query()
                ->whereNotIn('id', $usersFollowed)
                ->get(),
        ]);
    }
}
