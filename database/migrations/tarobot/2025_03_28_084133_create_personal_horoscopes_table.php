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
        Schema::create('personal_horoscopes', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('horoscope_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_horoscopes');
    }
};
