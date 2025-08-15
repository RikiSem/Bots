<?php

namespace App\Http\Bots\RandomBot\src\Models;

use Illuminate\Database\Eloquent\Model;

class Blacklist extends Model
{
    public const TITLE = 'Разбан в Рандом Боте';
    public const DESCRIPTION = 'Оплата разбана за нарушение правил бота';
    public const CURRENCY = 'XTR';
    public const PRICE = 500;
    public const PAYLOAD = 'ban';

    public const REASONS = [
        'rules' => 'Нарушенеи правил бота',
    ];
    protected $connection = 'randomBot';
    protected $guarded = [];
}
