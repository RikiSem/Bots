<?php

namespace App\Models;

use DefStudio\Telegraph\Models\TelegraphBot as ModelsTelegraphBot;
use Illuminate\Database\Eloquent\Model;

class TelegraphBot extends ModelsTelegraphBot
{
    protected $connection = 'mysql';
}
