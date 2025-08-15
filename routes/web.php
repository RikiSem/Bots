<?php

use App\Http\Bots\TaroBot\src\Controllers\IndexController;
use Illuminate\Support\Facades\Route;

Route::get('/', [IndexController::class, 'show']);
