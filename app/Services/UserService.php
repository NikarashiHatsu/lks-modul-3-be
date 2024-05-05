<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public static function isOwnAccount(User $loggedInUser, User $userSearched): bool
    {
        return $loggedInUser->id == $userSearched->id;
    }

    public static function getFollowingStatus(User $loggedInUser, User $userSearched): string
    {
        $follow = FollowService::findFollowingRelationship($loggedInUser, $userSearched);

        if (empty($follow)) return 'not-following';

        return $follow->is_accepted
            ? 'following'
            : 'requested';
    }

    public static function isPrivate(User $userSearched): bool
    {
        return $userSearched->is_private;
    }

    public static function isStatusNotFollowingOrRequested(string $status): bool
    {
        return in_array($status, ['not-following', 'requested']);
    }
}