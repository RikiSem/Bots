<?php

namespace App\Http\Bots\RandomBot\src\Controllers;

use App\Http\Controllers\Controller;
use App\Models\TelegraphBot;
use App\Models\TelegraphChat;
use Http;
use Nette\Utils\Random;

class WaifuController extends BaseController
{
    public const GET_TAG_URL = 'https://api.waifu.im/tags';
    public const API_URL = 'https://api.waifu.im/search';

    public const NSFW_TAG_KEY = 'nsfw';
    public const VERSATILE_TAG_KEY = 'versatile';
    public array $nsfwTags = [];
    public array $versatileTag = [];
    public function __construct(TelegraphBot $bot)
    {
        parent::__construct($bot);
        $this->nsfwTags = $this->getTags(self::NSFW_TAG_KEY);
        $this->versatileTag = $this->getTags(self::VERSATILE_TAG_KEY);
    }

    protected function getTags(string $type): mixed
    {
        return json_decode(Http::get(self::GET_TAG_URL)->body(), true)[$type];
    }

    public function getRandomWaifu(int $chatId) {
        switch (rand(0,1)) {
            case 0:
                $this->sendNsfwPhoto($chatId);
                break;
            case 1:
                $this->sendSfwPhoto($chatId);
                break;
        }
    }

    protected function sendNsfwPhoto(int $chatId)
    {
        Http::post(sprintf($this->url . 'sendPhoto', $this->bot->token), [
            'chat_id' => $chatId,
            'photo' => $this->getNsfwWaifu()
        ]);
    }

    protected function sendSfwPhoto(int $chatId)
    {
        Http::post(sprintf($this->url . 'sendPhoto', $this->bot->token), [
            'chat_id' => $chatId,
            'photo' => $this->getSfwWaifu()
        ]);
    }

    protected function getNsfwWaifu()
    {
        return $this->send($this->getRandomTag($this->nsfwTags));
    }

    protected function getSfwWaifu()
    {
        return $this->send($this->getRandomTag($this->versatileTag));
    }

    protected function send(string $tag)
    {
        $response = Http::get(self::API_URL, [
            'included_tags' => [$tag]
        ]);
        if ($response->getStatusCode() !== 200) {
            $this->send($tag);
        }

        return json_decode($response, true)['images'][0]['url'];
    }

    protected function getRandomTag(array $array): string
    {
        return $array[array_rand($array)];
    }
}
