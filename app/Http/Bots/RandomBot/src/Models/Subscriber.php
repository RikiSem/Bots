<?php

namespace App\Http\Bots\RandomBot\src\Models;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    public const TITLE = 'Подписка на Рандом Бота';
    public const DESCRIPTION = 'Подписка дает доступ к загрузке и просмотру видео и блокированию нежелательных постов';
    public const CURRENCY = 'XTR';
    public const PRICE = 250;
    public const PAYLOAD = 'sub';

    protected $connection = 'randomBot';
    protected $guarded = [];

    public function subscriptionIsActiv(): bool
    {
        return ceil(Carbon::now()->diffInSeconds($this->end_sub)) > 0;
    }
}
