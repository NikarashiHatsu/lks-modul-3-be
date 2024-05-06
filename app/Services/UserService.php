<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    /**
     * Checks if the given user is the same as the logged-in user.
     *
     * @param  \App\Models\User  $loggedInUser The currently logged-in user.
     * @param  \App\Models\User  $userSearched The user being searched or viewed.
     * @return bool Returns true if the given user is the same as the logged-in user, otherwise false.
     */
    public static function isOwnAccount(User $loggedInUser, User $userSearched): bool
    {
        return $loggedInUser->id == $userSearched->id;
    }

    /**
     * Gets the following status between the logged-in user and the searched user.
     *
     * @param  \App\Models\User  $loggedInUser The currently logged-in user.
     * @param  \App\Models\User  $userSearched The user being searched or viewed.
     * @return string Returns the following status:
     *      - 'not-following' if the logged-in user is not following the searched user.
     *      - 'following' if the logged-in user is following the searched user and the follow request is accepted.
     *      - 'requested' if the logged-in user has sent a follow request to the searched user, pending acceptance.
     */
    public static function getFollowingStatus(User $loggedInUser, User $userSearched): string
    {
        $follow = FollowService::findFollowingRelationship($loggedInUser, $userSearched);

        if (empty($follow)) return 'not-following';

        return $follow->is_accepted
            ? 'following'
            : 'requested';
    }

    /**
     * Checks if the searched user's account is private.
     *
     * @param  \App\Models\User  $userSearched The user being searched or viewed.
     * @return bool Returns true if the searched user's account is private, otherwise false.
     */
    public static function isPrivate(User $userSearched): bool
    {
        return $userSearched->is_private;
    }

    /**
     * Checks if the given status indicates that the user is not following or has sent a follow request.
     *
     * @param  string  $status The status to check.
     * @return bool Returns true if the status indicates that the user is not following or has sent a follow request, otherwise false.
     */
    public static function isStatusNotFollowingOrRequested(string $status): bool
    {
        return in_array($status, ['not-following', 'requested']);
    }
}