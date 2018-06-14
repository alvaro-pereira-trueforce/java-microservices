<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTelegramSettingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('channels_settings', function (Blueprint $table) {
            $table->primary('uuid');
            $table->uuid('uuid')->unique();
            $table->uuid('channel_uuid')->unique();
            $table->string('hello_message')->nullable();
            $table->boolean('has_hello_message')->default(false);
            $table->boolean('required_user_info')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('channels_settings');
    }
}
