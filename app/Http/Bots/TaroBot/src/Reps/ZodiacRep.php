<?php


namespace App\Http\Bots\TaroBot\src\Reps;


use App\Http\Bots\TaroBot\src\Models\Horoscope;
use Carbon\Carbon;
use Exception;

class ZodiacRep
{
    public const DAY_PERIOD = 'day';
    public const MONTH_PERIOD = 'month';
    public const YEAR_PERIOD = 'year';

    public const SEPARATOR = '|';

    public static function getDailyHoroscopeBySing(string $sing): Horoscope|null
    {
        try {
            $result = Horoscope::where('zodiac_sing', '=', $sing)
                ->where('period', '=', self::DAY_PERIOD)
                ->get()
                ->random();
        } catch (Exception $e) {
            $result = null;
        }

        return $result;
    }
    public static function getYearlyHoroscopeBySing(string $sing): Horoscope|null
    {
        try {
            $result =  Horoscope::where('zodiac_sing', '=', $sing)
                ->where('period', '=', self::YEAR_PERIOD)
                ->get()
                ->random();
        } catch (Exception $e) {
            $result = null;
        }

        return $result;
    }
    public static function getMonthlyHoroscopeBySing(string $sing): Horoscope|null
    {
        try {
            $result =  Horoscope::where('zodiac_sing', '=', $sing)
                ->where('period', '=', self::MONTH_PERIOD)
                ->get()
                ->random();
        } catch (Exception $e) {
            $result = null;
        }

        return $result;
    }

    public static function getCount(): int
    {
        return Horoscope::all()->count();
    }
    public static function create(string $text, string $sing, string $period)
    {
        Horoscope::create([
            'zodiac_sing' => $sing,
            'text' => $text,
            'period' => $period
        ])->save();
    }
}
