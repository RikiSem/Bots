<?php

namespace App\Http\Bots\RandomBot\src\Controllers;

use App\Http\Bots\RandomBot\src\Confs;
use App\Http\Controllers\Controller;
use App\Models\TelegraphBot;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class LoggerController extends BaseController
{
    public function __construct(TelegraphBot $bot) {
        parent::__construct($bot);
    }
    public function log(string $text)
    {
        Http::post(sprintf(self::URL . 'sendMessage', $this->bot->token), [
            'chat_id' => Confs::LOG_CHANNEL,
            'text' => sprintf('<strong>%s</strong> - %s', Carbon::now()->format('d.m.y H:m'), $text),
            'parse_mode' => 'html'
        ]);
    }
}
