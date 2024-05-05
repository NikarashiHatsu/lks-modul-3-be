<?php

namespace App\Services;

use App\Models\Follow;
use App\Models\User;
use Illuminate\Support\Collection;

class FollowService
{
    /**
     * Checks if a user is trying to follow themselves.
     *
     * @param  App\Models\User  $follower   The user attempting to follow.
     * @param  App\Models\User  $following  The user being followed.
     * @return bool Returns true if the user is attempting to follow themselves, otherwise false.
     */
    public static function isFollowingSelf(User $follower, User $following): bool
    {
        return $follower->id == $following->id;
    }

    /**
     * Checks if a user is already following another user.
     *
     * @param  \App\Models\User  $follower   The user who is potentially following
     * @param  \App\Models\User  $following  The user who is potentially being followed.
     * @return \App\Models\Follow|null  Returns the Follow model instance if the user is already following, otherwise null.
     */
    public static function isAlreadyFollowing(User $follower, User $following): ?Follow
    {
        $userFollowed = Follow::query()
            ->where('follower_id', $follower->id)
            ->where('following_id', $following->id)
            ->first();

        return $userFollowed;
    }

    /**
     * Creates a follow relationship between two users.
     *
     * @param  \App\Models\User  $follower   The user who is following.
     * @param  \App\Models\User  $following  The user who is being followed.
     * @return \App\Models\Follow  Returns the Follow model instance representing the new follow relationship.
     */
    public static function follow(User $follower, User $following): Follow
    {
        return Follow::create([
            'follower_id' => $follower->id,
            'following_id' => $following->id,
        ]);
    }

    /**
     * Removes a follow relationship between two users.
     *
     * @param  \App\Models\User  $follower   The user who is currently following.
     * @param  \App\Models\User  $following  The user who is currently being followed.
     * @return bool Returns true if the follow relationship is successfully removed, otherwise false.
     */
    public static function unfollow(User $follower, User $following): bool
    {
        return Follow::query()
            ->where('follower_id', $follower->id)
            ->where('following_id', $following->id)
            ->delete();
    }

    /**
     * Retrieves the users that the given user is following.
     *
     * @param  \App\Models\User  $follower  The user whose followings are to be retrieved.
     * @return \Illuminate\Support\Collection Returns a collection of users that the given user is following.
     */
    public static function following(User $user): Collection
    {
        /** @var \Illuminate\Database\Eloquent\Collection */
        $followings = $user->followings;

        return $followings
            ->map(function (Follow $follow) use ($user) {
                $userData = $follow->following;

                // To determine if the logged-in user is already being followed
                // by $follow->following (the user being followed), we need to
                // reverse the comparison, checking if $follow->following is
                // following the logged-in user ($user).
                $userData['is_requested'] = self::isRequested($follow->following, $user);

                return $userData;
            })
            ->values();
    }

    /**
     * Checks if a follow relationship is requested between two users.
     *
     * @param  \App\Models\User  $follower   The user who initiated the follow request.
     * @param  \App\Models\User  $following  The user who is being followed.
     * @return bool Returns true if a follow relationship is requested between the two users, otherwise false.
     */
    public static function isRequested(User $follower, User $following): bool
    {
        return Follow::query()
            ->where('follower_id', $follower->id)
            ->where('following_id', $following->id)
            ->exists();
    }

    /**
     * Finds the follow relationship where the given user is the follower and the other user is being followed.
     *
     * @param  \App\Models\User  $loggedInUser  The user who logged in
     * @param  \App\Models\User  $follower      The user who is potentially following.
     * @return \App\Models\Follow|null  Returns the Follow model instance representing the follow relationship if found, otherwise null.
     */
    public static function findFollowingRelationship(User $loggedInUser, User $follower): ?Follow
    {
        return Follow::query()
            ->where('follower_id', $follower->id)
            ->where('following_id', $loggedInUser->id)
            ->first();
    }

    /**
     * Checks if a user is not following another user.
     *
     * @param  \App\Models\User  $loggedInUser  The user who logged in
     * @param  \App\Models\User  $follower      The user who is potentially following.
     * @return bool  Returns true if the $following user is following the $follower user, otherwise false.
     */
    public static function isUserNotFollowing(User $loggedInUser, User $follower): bool
    {
        return empty(self::findFollowingRelationship($loggedInUser, $follower));
    }

    /**
     * Checks if a follow request between two users has already been accepted.
     *
     * @param  \App\Models\User  $loggedInUser  The user who logged in
     * @param  \App\Models\User  $follower      The user who is potentially following.
     * @return bool  Returns true if the follow request has already been accepted, otherwise false.
     */
    public static function isFollowRequestAlreadyAccepted(User $loggedInUser, User $follower): bool
    {
        $following = self::findFollowingRelationship($loggedInUser, $follower);

        return $following?->is_accepted ?? false;
    }

    /**
     * Accepts a follow request between two users.
     *
     * @param  \App\Models\User  $loggedInUser  The user who logged in
     * @param  \App\Models\User  $follower      The user who is potentially following.
     * @return bool  Returns true if the follow request is successfully accepted, otherwise false.
     */
    public static function accept(User $loggedInUser, User $follower): bool
    {
        $following = self::findFollowingRelationship($loggedInUser, $follower);

        return $following->update([
            'is_accepted' => true,
        ]);
    }

    /**
     * Retrieves the users who are following the given user.
     *
     * @param  \App\Models\User  $user The user whose followers are to be retrieved.
     * @return \Illuminate\Suppoer\Collection  Returns a collection of users who are following the given user.
     */
    public static function followers(User $user): Collection
    {
        /** @var \Illuminate\Database\Eloquent\Collection */
        $followers = $user->followers;

        return $followers
            ->map(function (Follow $follow) use ($user) {
                $userData = $follow->follower;

                $userData['is_requested'] = self::isRequested($follow->follower, $user);

                return $userData;
            })
            ->values();
    }
}