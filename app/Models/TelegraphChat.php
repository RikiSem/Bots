<?php

namespace App\Models;

use DefStudio\Telegraph\Models\TelegraphChat as BaseModel;
use Illuminate\Database\Eloquent\Model;

class TelegraphChat extends BaseModel
{
    protected $connection = 'mysql';
    protected $fillable = [
        'name',
        'chat_id',
        'telegraph_bot_id',
    ];
}
