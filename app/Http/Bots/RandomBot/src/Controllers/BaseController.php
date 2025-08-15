<?php

namespace App\Http\Bots\RandomBot\src\Controllers;

use App\Http\Bots\RandomBot\src\Confs;
use App\Http\Bots\RandomBot\src\Reps\PostBlacklistRep;
use App\Http\Bots\RandomBot\src\Reps\PostRep;
use App\Http\Controllers\Controller;
use App\Models\TelegraphBot;
use App\Models\TelegraphChat;
use DefStudio\Telegraph\Client\TelegraphResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BaseController extends Controller
{
    public const URL = 'https://api.telegram.org/bot%s/';
    public string $url = self::URL;
    public function __construct(
        public TelegraphBot $bot
    ){}

    public function save(string $fileId, int $userId, string $type): void
    {
        if (!PostRep::isFileIdExist($fileId)) {
            PostRep::savePost($type, $fileId, $userId);
            $this->sendToChannel($userId,$fileId, $type);
        } else {
            throw new \Exception('Такой файл уже существует');
        }
        
    }

    public function sendToChannel(int $userId, string $fileId, string $type): void
    {
        switch ($type) {
            case PostRep::AUDIO_TYPE:
                Http::post(sprintf($this->url . 'sendAudio', $this->bot->token), [
                    'chat_id' => Confs::PHOTO_CHANNEL,
                    'audio' => $fileId,
                    'caption' => sprintf("file_id - %s\nuser_id - %s", $fileId, $userId),
                    'parse_mode' => 'html'
                ]);  
                break;
            case PostRep::PHOTO_TYPE:
                Http::post(sprintf($this->url . 'sendPhoto', $this->bot->token), [
                    'chat_id' => Confs::PHOTO_CHANNEL,
                    'photo' => $fileId,
                    'caption' => sprintf("file_id - %s\nuser_id - %s", $fileId, $userId),
                    'parse_mode' => 'html'
                ]);  
                break;
            case PostRep::VIDEO_NOTE_TYPE:
                Http::post(sprintf($this->url . 'sendVideo', $this->bot->token), [
                    'chat_id' => Confs::VIDEO_CHANNEL,
                    'video' => $fileId,
                    'caption' => sprintf("file_id - %s\nuser_id - %s", $fileId, $userId),
                    'parse_mode' => 'html'
                ]); 
                Http::post(sprintf($this->url . 'sendMessage', $this->bot->token), [
                    'chat_id' => Confs::VIDEO_CHANNEL,
                    'text' => sprintf("/\/\/\ file_id - %s\nuser_id - %s", $fileId, $userId),
                    'parse_mode' => 'html'
                ]);  
                break; 
            case PostRep::VIDEO_TYPE:
                Http::post(sprintf($this->url . 'sendVideo', $this->bot->token), [
                    'chat_id' => Confs::VIDEO_CHANNEL,
                    'video' => $fileId,
                    'caption' => sprintf("file_id - %s\nuser_id - %s", $fileId, $userId),
                    'parse_mode' => 'html'
                ]);  
                break;
        }      
    }    

    public function isPostBanned(int $postId, int $userId): bool
    {
        return PostBlacklistRep::isBlocked($postId, $userId);
    }
}
