<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'taroBot';
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->integer('telegram_id');
            $table->string('name')->nullable(true);
            $table->string('sex')->nullable(true);
            $table->integer('age')->default(0);
            $table->timestamp('birthday')->nullable(true);
            $table->string('zodiac_horoscope')->nullable(true);
            $table->string('chinese_horoscope')->nullable(true);
            $table->boolean('set_info_state')->default(true);
            $table->boolean('set_name_state')->default(false);
            $table->boolean('set_birthday_state')->default(false);
            $table->boolean('set_sex_state')->default(false);
            $table->boolean('set_select_amulet_lvl_state')->default(false);
            $table->integer('selected_amulet_lvl')->default(0);
            $table->integer('selected_amulet_type')->default(0);
            $table->timestamps();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
