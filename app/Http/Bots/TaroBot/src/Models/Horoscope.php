<?php

namespace App\Http\Bots\TaroBot\src\Models;

use Illuminate\Database\Eloquent\Model;

class Horoscope extends Model
{
    protected $connection = 'taroBot';
    protected $guarded = [];
}
