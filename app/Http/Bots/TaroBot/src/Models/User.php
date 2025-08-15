<?php

namespace App\Http\Bots\TaroBot\src\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    protected $connection = 'taroBot';
    protected $guarded = [];

    public function setName(string $name): User
    {
        $this->name = $name;
        return $this;
    }
    public function setAge(int $age): User
    {
        $this->age = $age;
        return $this;
    }

    public function setSex(string $sex): User
    {
        $this->sex = $sex;
        return $this;
    }

    public function setBirthday(string $birthday): User
    {
        $this->birthday = $birthday;
        return $this;
    }

    public function sub():HasOne
    {
        return $this->hasOne(Subscriber::class, 'user_id', 'id');
    }

    public function personalHoroscopes(): HasMany
    {
        return $this->hasMany(PersonalHoroscope::class, 'user_id', 'id');
    }
}
