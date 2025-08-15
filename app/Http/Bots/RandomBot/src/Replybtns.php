<?php

namespace App\Http\Bots\RandomBot\src;

use DefStudio\Telegraph\Keyboard\ReplyButton;
use DefStudio\Telegraph\Keyboard\ReplyKeyboard;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class Replybtns
{
    public const START_COMMAND = '/start';
    public const BUY_COMMAND = '/buy';
    public const RULES_COMMAND = '/rules';
    public const TERMS_COMMAND = '/terms';
    public const RANDOM_FOTO_TEXT = 'Случайное фото';
    public const RANDOM_VIDEO_TEXT = 'Случайное видео';
    public const RANDOM_AUDIO_TEXT = 'Случайное аудио';
    public const RANDOM_VIDEO_NOTE_TEXT = 'Случайный кружок';
    public const LOAD_FOTO_TEXT = 'Загрузить фото';
    public const LOAD_VIDEO_TEXT = 'Загрузить видео';
    public const LOAD_AUDIO_TEXT = 'Загрузить аудио';
    public const LOAD_VIDEO_NOTE_TEXT = 'Загрузить кружок';
    public const BUY_PREM_TEXT = 'Полный доступ';
    public const BAN_USER_TEXT = 'Заблокировать юзера';
    public const BAN_POST_TEXT = 'Заблокировать пост';
    public const UNBAN_USER_TEXT = 'Разбан';

    public static function defaultBtns(): ReplyKeyboard
    {
        return ReplyKeyboard::make()->row([
            ReplyButton::make(self::RANDOM_FOTO_TEXT),
            ReplyButton::make(self::LOAD_FOTO_TEXT),
            ReplyButton::make(self::BUY_PREM_TEXT),
        ])->resize();
    }

    public static function premiumBtns(): ReplyKeyboard
    {
        return ReplyKeyboard::make()->row([
            ReplyButton::make(self::RANDOM_FOTO_TEXT),
            ReplyButton::make(self::LOAD_FOTO_TEXT),
        ])->row([
            ReplyButton::make(self::RANDOM_VIDEO_TEXT),
            ReplyButton::make(self::LOAD_VIDEO_TEXT),
        ])->row([
            ReplyButton::make(self::RANDOM_VIDEO_NOTE_TEXT),
            ReplyButton::make(self::LOAD_VIDEO_NOTE_TEXT),
        ])->row([
            ReplyButton::make(self::RANDOM_AUDIO_TEXT),
            ReplyButton::make(self::LOAD_AUDIO_TEXT),
        ])->resize();
    }

    public static function adminBtns(): ReplyKeyboard
    {
        return ReplyKeyboard::make()->row([
            ReplyButton::make(self::RANDOM_FOTO_TEXT),
            ReplyButton::make(self::LOAD_FOTO_TEXT),
        ])->row([
            ReplyButton::make(self::RANDOM_VIDEO_TEXT),
            ReplyButton::make(self::LOAD_VIDEO_TEXT),
        ])->row([
            ReplyButton::make(self::RANDOM_VIDEO_NOTE_TEXT),
            ReplyButton::make(self::LOAD_VIDEO_NOTE_TEXT),
        ])->row([
            ReplyButton::make(self::RANDOM_AUDIO_TEXT),
            ReplyButton::make(self::LOAD_AUDIO_TEXT),
        ])->row([
            ReplyButton::make(self::BAN_USER_TEXT),
        ])->resize();
    }

    public static function unBanBtns(): ReplyKeyboard
    {
        return ReplyKeyboard::make()
            ->row([
                ReplyButton::make(self::UNBAN_USER_TEXT),
            ])
            ->resize();
    }
    public static function postInlineBtns(int $postId): array
    {
        return [
            'inline_keyboard' => [
                [
                    [
                        'text' => 'Заблокировать пост',
                        'callback_data' => $postId
                    ],
                ],
            ]
        ];
    }
}
