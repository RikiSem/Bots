<?php

namespace App\Http\Bots\RandomBot\src;

class Texts
{
    public const PARTNER_PERCENT = 50;
    public const RULES = [
        '1 - Не загружать рекламу',
        '2 - Не загружать клоны одного и того же фото/видео',
        '3 - Не загружать ЦП'
    ];

    public const TERMS = "Используя данный бот, вы автоматически соглашаетесь в положениями пользовательского соглашения\nТекст пользовательского соглашения доступен ниже\nhttps://docs.google.com/document/d/1TNsyVFcinzruBtW_MRIB9seAoTHsUyLjKTUWjX02bRg/edit?usp=sharing";
    public const NOTIFICATION = [
        "Оформи подписку и получишь доступ к сотням <u>видео</u>(в том числе 18+), <u>аудио</u>, <u>кружкам</u>\nЖми -> /buy",
        "Ты можешь стать участником партнерской программы и получать 50% от трат тех, кто пройдет по твоей ссылке и купит подписку\nДля этого нажми на название бота и выбери `Партнерская программа`"
    ];
    public static function greatingText(string $username): string
    {
        return sprintf('Привет, %s', "<strong>{$username}</strong>!");
    }

    public static function pressBtn(string $btn)
    {
        return sprintf('Cначала нажми кнопку <strong>%s</strong>', $btn);
    }
}
