<?php

namespace App\Http\Bots\RandomBot\src\Controllers;

use App\Http\Bots\RandomBot\src\Confs;
use App\Http\Bots\RandomBot\src\Reps\PostRep;
use App\Http\Bots\RandomBot\src\ReplyBtns;
use App\Http\Controllers\Controller;
use App\Http\Reps\ChatRep;
use App\Models\TelegraphBot;
use App\Models\TelegraphChat;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class VideoController extends BaseController
{
    public function send(TelegraphChat $chat, bool $protected = true)
    {
        try {
            $post = PostRep::getRandomVideo();
            if ($post->blocked->where('user_id', '=', $chat->chat_id)->isEmpty()) {
                $response = Http::post(sprintf($this->url . 'sendVideo', $this->bot->token), [
                    'chat_id' => $chat->chat_id,
                    'video' => $post->file_id,
                    'reply_markup' => json_encode(ReplyBtns::postInlineBtns($post->id)),
                    'protect_content' => $protected
                ]);
                if (!$response->ok()) {
                    throw new Exception($response->body());
                }
            } else {
                throw new Exception('Видео больше нет(');
            }
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    public function sendNote(TelegraphChat $chat, bool $protected = true)
    {
        try {
            $post = PostRep::getRandomVideoNote();
            if ($post->blocked->where('user_id', '=', $chat->chat_id)->isEmpty()) {
                $response = Http::post(sprintf($this->url . 'sendVideo', $this->bot->token), [
                    'chat_id' => $chat->chat_id,
                    'video' => $post->file_id,
                    'reply_markup' => json_encode(ReplyBtns::postInlineBtns($post->id)),
                    'protect_content' => $protected
                ]);
                if (!$response->ok()) {
                    throw new Exception($response->body());
                }
            } else {
                throw new Exception('Кружков больше нет(');
            }
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }
}
