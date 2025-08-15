<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class IndexController extends Controller
{
    public function show()
    {
        $data = [
            [
                'name' => 'TaroBot',
                'img' => Storage::url('index/tarobot.png'),
                'url' => 'https://t.me/rus_taro_bot'
            ],
            [
                'name' => 'Рандом Бот',
                'img' => Storage::url('index/randombot.png'),
                'url' => 'https://t.me/RandomniyPostBot'
            ],
        ];
        return view('welcome', compact('data'));
    }
}
