<?php


namespace App\Http\Bots\TaroBot\src;

use Illuminate\Support\Facades\Http;

class PaidMedia
{
    public static function sendPaidMedia(string $token, int $chatId, string $content, int $price, $caption = '')
    {
        $url = config('telegraph.telegram_api_url') . "bot{$token}/sendPaidMedia";

        Http::post($url, [
            'chat_id' => $chatId,
            'media' => [
                [
                    'type' => 'photo',
                    'media' => $content,
                ]
            ],
            'caption' => $caption,
            'star_count' => $price,
            'protect_content' => false
        ]);
    }

    public static function answerPreCheckoutQuery(string $token, int $preCheckoutQueryId)
    {
        $url = config('telegraph.telegram_api_url') . "bot{$token}/answerPreCheckoutQuery";

        Http::post($url, [
            'pre_checkout_query_id' => $preCheckoutQueryId,
            'ok' => true,
        ]);
    }
}
