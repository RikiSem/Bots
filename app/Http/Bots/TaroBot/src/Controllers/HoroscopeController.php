<?php

namespace App\Http\Bots\TaroBot\src\Controllers;

use App\Http\Bots\TaroBot\src\HoroscopeGenerator;
use App\Http\Bots\TaroBot\src\Reps\PersonalHoroscopeRep;
use App\Http\Bots\TaroBot\src\Reps\ZodiacRep;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HoroscopeController extends Controller
{
    public const DEFAULT_FILE = 'horos.json';
    public function generateHoros()
    {
        HoroscopeGenerator::generate();
        return ZodiacRep::getCount();
    }

    public function addFromFile(Request $request)
    {
        try {
            $data = Storage::get('jsons/' . $request->filename ?? self::DEFAULT_FILE);
            if ($data === null) {
                throw new Exception('File not found');
            }
            foreach (json_decode($data, true) as $horoscope) {
                ZodiacRep::create(
                    $horoscope['text'],
                    $horoscope['sign'],
                    $horoscope['period']
                );
            }
            $result = ZodiacRep::getCount();
        } catch (Exception $e) {
            $result = $e->getMessage();
        }

        return $result;
    }

    public function resetDailyPersonalHoro(Request $request) {
        return PersonalHoroscopeRep::resetDailyPersonalHoro();
    }
    public function resetMonthlyPersonalHoro(Request $request) {
        return PersonalHoroscopeRep::resetMonthlyPersonalHoro();
    }
    public function resetYearlyPersonalHoro(Request $request) {
        return PersonalHoroscopeRep::resetYearlyPersonalHoro();
    }
    public function resetPersonalHoro(Request $request) {
        return PersonalHoroscopeRep::resetPersonalHoros();
    }
}
