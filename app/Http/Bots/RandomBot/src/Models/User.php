<?php

namespace App\Http\Bots\RandomBot\src\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Model
{
    protected $connection = 'randomBot';
    protected $guarded = [];

    public function sub(): HasOne
    {
        return $this->hasOne(Subscriber::class, 'user_id', 'id');
    }

    public function blockLoadAllEntities()
    {
        $this->blockLoadVideo();
        $this->blockLoadVideoNote();
        $this->blockLoadPhoto();
        $this->blockLoadAudio();
    }

    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    public function canBanPost(): bool
    {
        return $this->on_post_ban;
    }
    public function unblockPostBan()
    {
        $this->on_post_ban = true;
        $this->update();
    }
    public function blockPostBan()
    {
        $this->on_post_ban = false;
        $this->update();
    }

    public function canLoadPhoto(): bool
    {
        return $this->can_send_photo;
    }

    public function canLoadVideo(): bool
    {
        return $this->can_send_video;
    }

    public function canLoadVideoNote(): bool
    {
        return $this->can_send_video_note;
    }

    public function canLoadAudio(): bool
    {
        return $this->can_send_audio;
    }

    public function unblockLoadPhoto()
    {
        $this->can_send_photo = true;
        $this->update();
    }
    public function blockLoadPhoto()
    {
        $this->can_send_photo = false;
        $this->update();
    }

    public function unblockLoadVideo()
    {
        $this->can_send_video = true;
        $this->update();
    }
    public function blockLoadVideo()
    {
        $this->can_send_video = false;
        $this->update();
    }

    public function unblockLoadVideoNote()
    {
        $this->can_send_video_note = true;
        $this->update();
    }
    public function blockLoadVideoNote()
    {
        $this->can_send_video_note = false;
        $this->update();
    }

    public function unblockLoadAudio()
    {
        $this->can_send_audio = true;
        $this->update();
    }
    public function blockLoadAudio()
    {
        $this->can_send_audio = false;
        $this->update();
    }
}
