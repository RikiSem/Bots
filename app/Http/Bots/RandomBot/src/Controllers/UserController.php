<?php

namespace App\Http\Bots\RandomBot\src\Controllers;

use App\Http\Bots\RandomBot\src\Models\Blacklist;
use App\Http\Bots\RandomBot\src\Reps\BlacklistRep;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function blockUser(int $userId)
    {
        BlacklistRep::block($userId);
    }

    public function isBlocked(int $userId): Blacklist|null
    {
        return BlacklistRep::getUser($userId);
    }

    public function unBlockUser(int $userId) {
        BlacklistRep::unBanUser($userId);
    }
}
