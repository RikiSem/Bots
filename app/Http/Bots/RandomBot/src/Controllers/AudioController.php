<?php

namespace App\Http\Bots\RandomBot\src\Controllers;

use App\Http\Bots\RandomBot\src\Replybtns;
use App\Http\Bots\RandomBot\src\Reps\PostRep;
use App\Http\Controllers\Controller;
use App\Models\TelegraphChat;
use Exception;
use Illuminate\Support\Facades\Http;

class AudioController extends BaseController
{
    public function send(TelegraphChat $chat, bool $protected = true)
    {
        try {
            $post = PostRep::getRandomAudio();
            if ($post->blocked->where('user_id', '=', $chat->chat_id)->isEmpty()) {
                $response = Http::post(sprintf($this->url . 'sendAudio', $this->bot->token), [
                    'chat_id' => $chat->chat_id,
                    'audio' => $post->file_id,
                    'reply_markup' => json_encode(ReplyBtns::postInlineBtns($post->id)),
                    'protect_content' => $protected
                ]);
                if (!$response->ok()) {
                    throw new Exception($response->body());
                }
            } else {
                throw new Exception('Аудио больше нет(');
            }
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }
}
