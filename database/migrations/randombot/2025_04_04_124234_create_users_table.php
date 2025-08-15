<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'randomBot';
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('telegram_id')->nullable(true);
            $table->string('username')->nullable(true);
            $table->boolean('is_admin')->default(false);
            $table->boolean('can_send_video')->default(false);
            $table->boolean('can_send_video_note')->default(false);
            $table->boolean('can_send_photo')->default(false);
            $table->boolean('can_send_audio')->default(false);
            $table->boolean('on_post_ban')->default(false);
            $table->json('params')->nullable(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
