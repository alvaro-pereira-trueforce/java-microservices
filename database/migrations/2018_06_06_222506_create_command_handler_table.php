<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommandHandlerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('telegram_command_handler', function (Blueprint $table) {
            $table->primary('uuid');
            $table->uuid('uuid')->unique();
            $table->string('user_id');
            $table->string('chat_id');
            $table->string('command');
            $table->string('state');
            $table->string('content')->nullable();
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
        Schema::dropIfExists('telegram_command_handler');
    }
}
