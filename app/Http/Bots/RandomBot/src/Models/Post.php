<?php

namespace App\Http\Bots\RandomBot\src\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $connection = 'randomBot';
    protected $guarded = [];

    public function blocked()
    {
        return $this->hasMany(PostBlacklist::class, 'post_id', 'id');
    }
}
