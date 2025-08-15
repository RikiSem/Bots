<?php


namespace App\Http\Bots\TaroBot\src\Reps;

use App\Http\Bots\TaroBot\src\Models\User;

class UserRep
{
    public static function isUserExist(int $telegramId): bool
    {
        return !User::where('telegram_id', '=', $telegramId)->get()->isEmpty();
    }
    public static function createUser(int $telegramId, string $username): User
    {
        $user = new User();
        $user->username = $username;
        $user->telegram_id = $telegramId;
        $user->set_info_state = 1;
        $user->set_name_state = 0;
        $user->set_birthday_state = 0;
        $user->set_sex_state = 0;
        $user->save();
        return User::find($user->id)->first();
    }

    public static function getUser(int $telegramId): User
    {
        return User::where('telegram_id', '=', $telegramId)->first();
    }
}
