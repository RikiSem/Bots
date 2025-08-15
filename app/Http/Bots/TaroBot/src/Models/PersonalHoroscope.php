<?php

namespace App\Http\Bots\TaroBot\src\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PersonalHoroscope extends Model
{
    protected $connection = 'taroBot';
    protected $guarded = [];

    public function horoscope(): HasOne
    {
        return $this->hasOne(Horoscope::class, 'id', 'horoscope_id');
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
