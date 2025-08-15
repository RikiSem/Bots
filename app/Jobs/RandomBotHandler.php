<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\Request;
use App\Http\Bots\RandomBot\RandomBot;
use App\Models\TelegraphBot;

class RandomBotHandler implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public array $request,
        public TelegraphBot $bot
        )
    {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $randomBot = new RandomBot($this->bot);
        $randomBot->handler($this->request);
    }
}
