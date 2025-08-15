<?php

namespace App\Http\Bots\TaroBot\src\Models;

use App\Http\Bots\TaroBot\src\Reps\SubscriberRep;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Subscriber extends Model
{
    protected $connection = 'taroBot';
    public const TITLE = 'Подписка';
    public const DESCRIPTION = 'Покупка подписки для доступа к персональным гороскопам';
    public const CURRENCY = 'XTR';
    public const PAYMENT_PAYLOAD = 'subscription';
    public const PAYMENT_COST = 250;
    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function subscriptionIsActiv(): bool
    {
        return !ceil(Carbon::now()->diffInSeconds(Carbon::createFromTimestamp($this->end_sub))) <= 0;
    }
}
