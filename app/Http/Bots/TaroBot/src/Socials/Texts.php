<?php


namespace App\Http\Bots\TaroBot\src\Socials;


class Texts
{

    public const AMULETS = [
        'weak' => 'низшего',
        'midd' => 'среднего',
        'high' => 'высшего'
    ];

    public const SUCCESSFUL_PAYMENT_TEXT = 'Поздравляем, подписка оформлена на месяц';
    public const NOT_SUBSCRIBER_TEXT = 'У тебя еще нет оформлена подписка или она уже кончилась. Оформи ее и сможешь пользоваться всеми функциями сервиса)';

    public const NOTIFICATIONS = [
        'partner_link' => 'Напиминаю тебе, что ты можешь заработать <strong><u>50%</u></strong> от трат тех, кто перейдет по твоей партнерской ссылке и что то купит в боте',
        'personal_horo' => 'Тут еще существует <strong><u>персональные гороскопы</u></strong>. Для них тебе надо только лишь купить подписку на месяц',
        'amulets' => 'У нас еще существуют <strong><u>цифровые обереги</u></strong>, которые помогут сохранить твое здоровье, поправить финансы или личную жизнь, а так же дать сил и здоровья родственникам'
    ];

    public static function greatingText(string $username): string
    {
        return "Приветствую, <strong>{$username}</strong>!\nЧем могу помочь?\nТы тут впервые, поэтому давай проведем первичную настройку. Потом изменить данные не получится.";
    }

    public static function defaultText(string $username): string
    {
        return "Приветствую, <strong>{$username}</strong>!\nЧем могу помочь?";
    }
}
