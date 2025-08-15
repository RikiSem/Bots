<?php

namespace App\Http\Bots\RandomBot\src\Reps;

use App\Http\Bots\RandomBot\src\Models\Blacklist;
use Exception;

class BlacklistRep
{
    
    public static function block(int $userId)
    {
        Blacklist::create([
            'user_id' => $userId,
            'reason' => Blacklist::REASONS['rules'],
        ])
            ->save();
    }

    public static function getUser(int $userId): Blacklist|null
    {
        return Blacklist::where('user_id', '=', $userId)->first();
    }

    public static function unBanUser(int $userId)
    {
        $user = self::getUser($userId);
        if ($user !== null) {
            $user->delete();
        } else {
            throw new Exception('Пользователь не заблокрован');
        }
    }
}
