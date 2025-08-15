<?php


namespace App\Http\Bots\TaroBot\src\Reps;

use App\Http\Bots\TaroBot\src\Models\Horoscope;
use App\Http\Bots\TaroBot\src\Models\PersonalHoroscope;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class PersonalHoroscopeRep
{
    public static function setHoroscopes(int $userId, string $sign) {
        if ($userId === null || $sign === null) {
            throw new Exception(message: 'Параметры null');
        }
        PersonalHoroscope::create([
            'user_id' => $userId,
            'horoscope_id' => ZodiacRep::getDailyHoroscopeBySing($sign)->id,
        ])->save();
        PersonalHoroscope::create([
            'user_id' => $userId,
            'horoscope_id' => ZodiacRep::getMonthlyHoroscopeBySing($sign)->id,
        ])->save();
        PersonalHoroscope::create([
            'user_id' => $userId,
            'horoscope_id' => ZodiacRep::getYearlyHoroscopeBySing($sign)->id,
        ])->save();
    }

    public static function getUserPersonalHoros(int $userId): Collection
    {
        return PersonalHoroscope::where('user_id', '=', $userId)->get();
    }

    public static function getDailyHoroByIserId(int $userId): Horoscope|null
    {
        $result = null;
        $userPersonalHoroCollection = self::getUserPersonalHoros($userId);
        foreach ($userPersonalHoroCollection as $personalHoro) {
            if ($personalHoro->horoscope->period === ZodiacRep::DAY_PERIOD) {
                $result = $personalHoro->horoscope;
            }
        }
        return $result;
    }

    public static function getMonthlyHoroByUserId(int $userId): Horoscope|null
    {
        $result = null;
        $userPersonalHoroCollection = self::getUserPersonalHoros($userId);
        foreach ($userPersonalHoroCollection as $personalHoro) {
            if ($personalHoro->horoscope->period === ZodiacRep::MONTH_PERIOD) {
                $result = $personalHoro->horoscope;
            }
        }
        return $result;
    }

    public static function getYearlyHoroByUserId(int $userId): Horoscope|null
    {
        $result = null;
        $userPersonalHoroCollection = self::getUserPersonalHoros($userId);
        foreach ($userPersonalHoroCollection as $personalHoro) {
            if ($personalHoro->horoscope->period === ZodiacRep::YEAR_PERIOD) {
                $result = $personalHoro->horoscope;
            }
        }
        return $result;
    }

    public static function resetDailyPersonalHoro()
    {
        $personalHoros = PersonalHoroscope::all();
        foreach ($personalHoros as $horo) {
            $sign = $horo->user->zodiac_horoscope;
            if ($horo->horoscope->period === ZodiacRep::DAY_PERIOD) {
                $horo->horoscope_id = ZodiacRep::getDailyHoroscopeBySing($sign)->id;
                $horo->update();
            }
        }
    }
    public static function resetMonthlyPersonalHoro()
    {
        $personalHoros = PersonalHoroscope::all();
        foreach ($personalHoros as $horo) {
            $sign = $horo->user->zodiac_horoscope;
            if ($horo->horoscope->period === ZodiacRep::MONTH_PERIOD) {
                $horo->horoscope_id = ZodiacRep::getMonthlyHoroscopeBySing($sign)->id;
                $horo->update();
            }
        }
    }
    public static function resetYearlyPersonalHoro()
    {
        $personalHoros = PersonalHoroscope::all();
        foreach ($personalHoros as $horo) {
            $sign = $horo->user->zodiac_horoscope;
            if ($horo->horoscope->period === ZodiacRep::YEAR_PERIOD) {
                $horo->horoscope_id = ZodiacRep::getYearlyHoroscopeBySing($sign)->id;
                $horo->update();
            }
        }
    }

    public static function resetPersonalHoros(): array
    {
        $result = [];
        $personalHoros = PersonalHoroscope::all();
        foreach ($personalHoros as $horo) {
            $sign = $horo->user->zodiac_horoscope;
            switch($horo->horoscope->period) {
                case ZodiacRep::DAY_PERIOD:
                    $horo->horoscope_id = ZodiacRep::getDailyHoroscopeBySing($sign)->id;
                    $horo->update();
                    break;
                case ZodiacRep::MONTH_PERIOD:
                    $horo->horoscope_id = ZodiacRep::getMonthlyHoroscopeBySing($sign)->id;
                    $horo->update();
                    break;
                case ZodiacRep::YEAR_PERIOD:
                    $horo->horoscope_id = ZodiacRep::getYearlyHoroscopeBySing($sign)->id;
                    $horo->update();
                    break;
            }

            $result[] = $horo->horoscope_id;
        }

        return $result;
    }
}
