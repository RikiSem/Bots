<?php

use App\Http\Bots\TaroBot\src\Controllers\BotController;
use App\Http\Bots\TaroBot\src\Controllers\HoroscopeController;
use DefStudio\Telegraph\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

Route::post(config('telegraph.webhook.url'), [BotController::class, 'handler'])
    //->middleware(config('telegraph.webhook.middleware', []))
    ->name('telegraph.webhook');

Route::prefix('/webhook')->group(function () {
    Route::get('/get', [\App\Http\Bots\TaroBot\src\Controllers\CheckController::class, 'getHooks']);
    Route::post('/set', [\App\Http\Bots\TaroBot\src\Controllers\CheckController::class, 'setWebHook']);
});

Route::prefix('/horo')->group(function () {
    Route::get('/gen', [HoroscopeController::class, 'generateHoros']);
    Route::get('/file', [HoroscopeController::class, 'addFromFile']);
    Route::get('/reset', [HoroscopeController::class, 'resetPersonalHoro']);
    Route::get('/reset/day', [HoroscopeController::class, 'resetDailyPersonalHoro']);
    Route::get('/reset/month', [HoroscopeController::class, 'resetMonthlyPersonalHoro']);
    Route::get('/reset/year', [HoroscopeController::class, 'resetYearlyPersonalHoro']);
});
