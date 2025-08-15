<?php

namespace App\Http\Bots\RandomBot\src\Models;

use Illuminate\Database\Eloquent\Model;

class PostBlacklist extends Model
{
    protected $connection = 'randomBot';
    protected $guarded = [];
}
