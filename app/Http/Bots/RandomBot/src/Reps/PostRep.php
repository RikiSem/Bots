<?php

namespace App\Http\Bots\RandomBot\src\Reps;

use App\Http\Bots\RandomBot\src\Models\Post;

class PostRep
{
    public const PHOTO_TYPE = 'photo';
    public const VIDEO_TYPE = 'video';
    public const AUDIO_TYPE = 'audio';
    public const VIDEO_NOTE_TYPE = 'video_note';

    public static function getRandomPhoto(): Post
    {
        return Post::where('type', self::PHOTO_TYPE)
            ->get()
            ->random();
    }

    public static function getRandomVideoNote(): Post
    {
        return Post::where('type', self::VIDEO_NOTE_TYPE)
            ->get()
            ->random();
    }

    public static function getRandomVideo(): Post
    {
        return Post::where('type', self::VIDEO_TYPE)
            ->get()
            ->random();
    }

    public static function getRandomAudio(): Post
    {
        return Post::where('type', self::AUDIO_TYPE)
            ->get()
            ->random();
    }

    public static function isFileIdExist(string $fileId): bool
    {
        return Post::where('file_id', '=', $fileId)->exists();
    }

    public static function savePost(string $type, string $fileId, int $userId)
    {
        $post = new Post();
        $post->type = $type;
        $post->file_id = $fileId;
        $post->user_id = $userId;
        $post->save();
    }


}
