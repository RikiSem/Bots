<?php

namespace App\Http\Controllers;

use App\Http\Bots\RandomBot\RandomBot;
use App\Http\Bots\TaroBot\TaroBot;
use App\Http\Reps\BotRep;
use App\Jobs\RandomBotHandler;
use DefStudio\Telegraph\Handlers\WebhookHandler;
use DefStudio\Telegraph\Models\TelegraphBot;
use Exception;
use Illuminate\Http\Request;

class BotController extends WebhookHandler
{
    public const FOTO_PATH = '/../../storage/app/public/storage/';

    public const BOTS = [
        TaroBot::BOT_NAME,
        RandomBot::BOT_NAME
    ];
    public function handler(
        Request $request,
        TaroBot $taroBot,
        BotRep $botRep,
        )
    {
        $result = response('done', 200);
        try{
            $bot = $botRep->getBotByToken($request->token);
            $botName = $bot->name;
            switch ($botName) {
                case TaroBot::BOT_NAME:
                    $taroBot->handler($request);
                    break;
                case RandomBot::BOT_NAME:
                    RandomBotHandler::dispatchSync(
                        $request->all(),
                        $bot
                    );
                    break;
            }
        } catch (Exception $e) {
            $result = response($e->getMessage(), 200);
            file_put_contents('error.txt', $e->getMessage());
        }
        return $result;
    }
}
