<?php

namespace App\Console\Commands;

use App\Http\Bots\RandomBot\src\Models\Subscriber;
use App\Http\Bots\RandomBot\src\Models\User;
use App\Http\Bots\RandomBot\src\Reps\SubscriberRep;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class transferOldSubsTableToNewFormat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:transfer-subs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Переносит подписчиков из JSONa старой БД в новую';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $subs = json_decode(Storage::get('jsons/oldDb.json'), true)['subscribers'];
        foreach ($subs as $key => $sub) {
            if (Carbon::parse($sub['end_sub'])->timestamp < Carbon::now()->timestamp) {
                continue;
            }
            $this->info(sprintf('adding sub with id %s', $sub['user_id']));
            try {

                $user = new User();
                $user->telegram_id = $sub['user_id'];
                $user->username = '';
                $user->save();

                $newSub = new Subscriber();
                $newSub->user_id = $user->id;
                $newSub->start_sub = Carbon::parse($sub['start_sub'])->timestamp;
                $newSub->end_sub = Carbon::parse($sub['end_sub'])->timestamp;
                $newSub->save();
            } catch (Exception $e) {
                $this->info($e->getMessage());
            }
        }

        $this->info('total count rows: ' . Subscriber::all()->count());
    }
}
