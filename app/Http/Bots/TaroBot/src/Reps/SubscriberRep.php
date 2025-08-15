<?php


namespace App\Http\Bots\TaroBot\src\Reps;

use App\Http\Bots\TaroBot\src\Models\PersonalHoroscope;
use App\Http\Bots\TaroBot\src\Models\Subscriber;
use Carbon\Carbon;
use Exception;

class SubscriberRep
{
    public static function setSubscription(int $userId)
    {
        $sub = self::getSubscriptionByUserId($userId);
        if ($sub === null) {
            Subscriber::create([
                'user_id' => $userId,
                'start_sub' => Carbon::now()->timestamp,
                'end_sub' => Carbon::now()->timestamp + (30 * 86400),
            ])
            ->save();
            $sub = Subscriber::find(self::getSubscriptionByUserId($userId)->id);
            PersonalHoroscopeRep::setHoroscopes(
                $sub->user->id,
                $sub->user->zodiac_horoscope
            );
        } else {
            if (self::userCanUsePaymentContent($sub)) {
                throw new Exception('У юзера уже оформлена подписка');
            }
            $sub->start_sub = Carbon::now()->timestamp;
            $sub->end_sub = Carbon::now()->timestamp + (30 * 86400);
            $sub->update();
        }
    }

    public static function getSubscriptionByUserId(int $userId): Subscriber|null
    {
        return Subscriber::where('user_id', '=', $userId)
            ->first();
    }
    public static function isSubscriber(int $userId): bool
    {
        $sub = self::getSubscriptionByUserId($userId);
        return true;
    }

    public static function userCanUsePaymentContent($subscriber): bool
    {
        return $subscriber !== null && $subscriber->subscriptionIsActiv();
    }
}
