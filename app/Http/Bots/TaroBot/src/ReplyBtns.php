<?php


namespace App\Http\Bots\TaroBot\src;


use DefStudio\Telegraph\Keyboard\ReplyButton;
use DefStudio\Telegraph\Keyboard\ReplyKeyboard;

class ReplyBtns
{
    public const COMMAND_START = '/start';
    public const COMMAND_HORO_DAY = '/horo_day';
    public const COMMAND_HORO_MONTH = '/horo_month';
    public const COMMAND_HORO_YEAR = '/horo_year';
    public const COMMAND_PERS_HORO_DAY = '/pers_horo_day';
    public const COMMAND_PERS_HORO_MONTH = '/pers_horo_month';
    public const COMMAND_PERS_HORO_YEAR = '/pers_horo_year';
    public const COMMAND_AMULETS = '/amulets';
    public const COMMAND_TERMS = '/terms';
    public const COMMAND_HELP = '/help';
    public const COMMAND_PROFILE = '/profile';

    public const MALE_TEXT = 'М';
    public const FEMALE_TEXT = 'Ж';
    public const TEXT_YES = 'Да';
    public const TEXT_NO = 'Нет';
    public const TEXT_SET_NAME = 'Указать имя';
    public const TEXT_SET_SEX = 'Указать пол';
    public const TEXT_SET_BIRTHDAY = 'Указать дату рождения';
    public const DAILY_HOROSCOPE_TEXT = 'Гороскоп на день';
    public const MONTHLY_HOROSCOPE_TEXT = 'Гороскоп на месяц';
    public const YEARLY_HOROSCOPE_TEXT = 'Гороскоп на год';
    public const PERSONAL_DAILY_HOROSCOPE_TEXT = 'Перс. гороскоп на день';
    public const PERSONAL_MONTHLY_HOROSCOPE_TEXT = 'Перс. гороскоп на месяц';
    public const PERSONAL_YEARLY_HOROSCOPE_TEXT = 'Перс. гороскоп на год';
    public const AMULETS_TEXT = 'Цифровые обереги';
    public const ON_MAIN_TEXT = 'На главную';
    public const SEE_PROFILE_TEXT = 'Посмотреть свой профиль';
    public const HEALTH_CATEGORY_TEXT = 'Здоровье';
    public const RELATIONSHIP_CATEGORY_TEXT = 'Личная жизнь';
    public const FINANCE_CATEGORY_TEXT = 'Финансы';
    public const FAMILY_WELL_BEING_CATEGORY_TEXT = 'Благополучие родственников';
    public const WEAK_AMULET_LVL_TEXT = 'Низший';
    public const MIDD_AMULET_LVL_TEXT = 'Средний';
    public const HIGH_AMULET_LVL_TEXT = 'Высший';
    public const PAY_SUB_TEXT = 'Купить подписку';

    public static function YesNoKeyBoard(): ReplyKeyboard
    {
        return ReplyKeyboard::make()->row([
            ReplyButton::make(self::TEXT_YES),
            ReplyButton::make(self::TEXT_NO),
        ])->resize();
    }
    public static function defaultKeyBoard(): ReplyKeyboard
    {
        return ReplyKeyboard::make()->buttons([
            ReplyButton::make(self::DAILY_HOROSCOPE_TEXT),
            ReplyButton::make(self::MONTHLY_HOROSCOPE_TEXT),
            ReplyButton::make(self::YEARLY_HOROSCOPE_TEXT),
            ReplyButton::make(self::PERSONAL_DAILY_HOROSCOPE_TEXT),
            ReplyButton::make(self::PERSONAL_MONTHLY_HOROSCOPE_TEXT),
            ReplyButton::make(self::PERSONAL_YEARLY_HOROSCOPE_TEXT),
            ReplyButton::make(self::AMULETS_TEXT),
            ReplyButton::make(self::SEE_PROFILE_TEXT),
        ])->resize();

    }

    public static function paySubKeyBoard(): ReplyKeyboard
    {
        return ReplyKeyboard::make()->row([
            ReplyButton::make(self::PAY_SUB_TEXT),
            ReplyButton::make(self::ON_MAIN_TEXT)
        ])->resize();
    }

    public static function selectSexKeyBoard(): ReplyKeyboard
    {
        return ReplyKeyboard::make()->row([
            ReplyButton::make(self::MALE_TEXT),
            ReplyButton::make(self::FEMALE_TEXT)
        ])->resize();
    }

    public static function amuletsLevelsKeyBoard(): ReplyKeyboard
    {
        return ReplyKeyboard::make()
            ->row([
                ReplyButton::make(self::WEAK_AMULET_LVL_TEXT),
                ReplyButton::make(self::MIDD_AMULET_LVL_TEXT),
                ReplyButton::make(self::HIGH_AMULET_LVL_TEXT),
            ])->row([
                ReplyButton::make(self::ON_MAIN_TEXT),
            ])->resize();
    }

    public static function amuletsCategoryKeyBoard(): ReplyKeyboard
    {
        return ReplyKeyboard::make()
            ->buttons([
                ReplyButton::make(self::HEALTH_CATEGORY_TEXT),
                ReplyButton::make(self::RELATIONSHIP_CATEGORY_TEXT),
                ReplyButton::make(self::FINANCE_CATEGORY_TEXT),
                ReplyButton::make(self::FAMILY_WELL_BEING_CATEGORY_TEXT),
                ReplyButton::make(self::ON_MAIN_TEXT),
            ])->resize();
    }

    public static function userSettingsKeyBoard(): ReplyKeyboard
    {
        return ReplyKeyboard::make()->row([
            ReplyButton::make(self::TEXT_SET_NAME),
            ReplyButton::make(self::TEXT_SET_SEX),
            ReplyButton::make(self::TEXT_SET_BIRTHDAY),
        ])->resize();
    }
}
