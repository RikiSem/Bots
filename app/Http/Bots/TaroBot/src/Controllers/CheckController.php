<?php

namespace App\Http\Bots\TaroBot\src\Controllers;

use App\Http\Controllers\Controller;
use DefStudio\Telegraph\Handlers\WebhookHandler;
use DefStudio\Telegraph\Models\TelegraphBot;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class CheckController extends Controller
{
    public function setWebHook(Client $client, Request $request) {
        $response = $client->post(config('telegraph.telegram_api_url') . 'bot' . TelegraphBot::find(1)->token . '/setWebhook', [
            'form_params' => [
                'url' => $request->url,
            ]
        ]);

        return $response->getBody();
    }

    public function getHooks(Client $client) {
        $response = $client->post(config('telegraph.telegram_api_url') . 'bot' . TelegraphBot::find(1)->token . '/getWebhookInfo', [
            'form_params' => [
                'url' => 'https://tarobot.tech/',
            ]
        ]);
        return $response->getBody();
    }
}
