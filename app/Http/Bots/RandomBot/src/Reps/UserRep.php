<?php


namespace App\Http\Bots\RandomBot\src\Reps;

use App\Http\Bots\RandomBot\src\Models\User;

class UserRep
{
    public function isUserExist(int $telegramId): bool
    {
        return !User::where('telegram_id', '=', $telegramId)->get()->isEmpty();
    }
    public function createUser(int $telegramId, string $username): User
    {
        $user = self::getUser($telegramId);
        if ($user === null)
        {
            $user = new User();
            $user->username = $username;
            $user->telegram_id = $telegramId;
            $user->save();
        }
        return $user;
    }

    public function getUser(int $telegramId): User|null
    {
        return User::where('telegram_id', '=', $telegramId)->first();
    }

    public function firstOrCreate(int $tgId, string $username): User
    {
        $attrs = [
            'username' => $username,
            'telegram_id' => $tgId
        ];
        return User::firstOrCreate($attrs, $attrs);
    }
}
