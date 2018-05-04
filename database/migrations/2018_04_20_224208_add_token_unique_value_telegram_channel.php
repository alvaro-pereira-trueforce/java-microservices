<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTokenUniqueValueTelegramChannel extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('telegram_channels', function (Blueprint $table) {
            $table->string('token')->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('telegram_channels', function (Blueprint $table) {
            $table->dropUnique('token');
        });
    }
}
