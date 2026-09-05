<?php

namespace App\Policies;

use App\Models\Tweet;
use App\Models\User;

class TweetPolicy
{
    /**
     * Determine whether the user can update the tweet.
     */
    public function update(User $user, Tweet $tweet): bool
    {
        return $user->id === $tweet->user_id;
    }

    /**
     * Determine whether the user can delete the tweet.
     */
    public function delete(User $user, Tweet $tweet): bool
    {
        return $user->id === $tweet->user_id;
    }
}
