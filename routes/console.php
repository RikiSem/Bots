<?php

use App\Http\Bots\TaroBot\src\Reps\PersonalHoroscopeRep;
use Carbon\Carbon;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    file_put_contents('cron_test.txt', Carbon::now()->format('d.m.Y H:m:s'));
})->everyMinute();


Schedule::call(function () {
    PersonalHoroscopeRep::resetDailyPersonalHoro();
})->daily();

Schedule::call(function () {
    PersonalHoroscopeRep::resetMonthlyPersonalHoro();
})->monthly();

Schedule::call(function () {
    PersonalHoroscopeRep::resetYearlyPersonalHoro();
})->yearly();
