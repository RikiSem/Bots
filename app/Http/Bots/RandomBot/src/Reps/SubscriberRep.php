<?php


namespace App\Http\Bots\RandomBot\src\Reps;

use App\Http\Bots\RandomBot\src\Models\Subscriber;
use Carbon\Carbon;
use Exception;

class SubscriberRep
{
    public function setSubscription(int $userId)
    {
        $sub = $this->getSubscriptionByUserId($userId);
        if ($sub === null) {
            Subscriber::create([
                'user_id' => $userId,
                'start_sub' => Carbon::parse(Carbon::now()->timestamp),
                'end_sub' => Carbon::parse(Carbon::now()->timestamp + (30 * 86400)),
            ])
            ->save();
        } else {
            if (self::userCanUsePaymentContent($sub)) {
                throw new Exception('У юзера уже оформлена подписка');
            }
            $sub->start_sub = Carbon::parse(Carbon::now()->timestamp);
            $sub->end_sub = Carbon::parse(Carbon::now()->timestamp + (30 * 86400));
            $sub->update();
        }
    }

    public function getSubscriptionByUserId(int $userId): Subscriber|null
    {
        return Subscriber::where('user_id', '=', $userId)
            ->first();
    }
    public function isSubscriber(int $userId): bool
    {
        $sub = $this->getSubscriptionByUserId($userId);
        return true;
    }

    public function userCanUsePaymentContent(Subscriber|null $subscriber): bool
    {
        return $subscriber !== null && $subscriber->subscriptionIsActiv();
    }
}
