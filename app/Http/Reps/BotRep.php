<?php

namespace App\Http\Reps;

use App\Models\TelegraphBot;

class BotRep
{
    public function getBotByToken(string $token): TelegraphBot
    {
        return TelegraphBot::where('token', '=',$token)->first();
    }

    public function getBotByName(string $name): TelegraphBot
    {
        return TelegraphBot::where('name', '=', $name)->first();
    }
}
