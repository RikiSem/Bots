<?php

namespace App\Http\Bots\RandomBot\src\Reps;

use App\Http\Bots\RandomBot\src\Models\PostBlacklist;

class PostBlacklistRep
{
    public static function banPost(int $postId, int $userId) {
        if (!PostBlacklistRep::isBlocked($postId, $userId)) {
            $bannedPost = new PostBlacklist();
            $bannedPost->user_id = $userId;
            $bannedPost->post_id = $postId;
            $bannedPost->save();
        }
    }

    public static function isBlocked(int $postId, int $userId): bool
    {
        return PostBlacklist::where('user_id', '=', $userId)
            ->where('post_id', '=', $postId)
            ->exists();
    }
}
