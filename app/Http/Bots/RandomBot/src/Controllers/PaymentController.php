<?php

namespace App\Http\Bots\RandomBot\src\Controllers;

use Illuminate\Support\Facades\Http;

class PaymentController extends BaseController
{
    public function answerPreCheckoutQuery(string $queryId)
    {
        Http::post(sprintf($this->url . 'answerPreCheckoutQuery', $this->bot->token), [
            'pre_checkout_query_id' => $queryId,
            'ok' => true
        ]);
    }
}
