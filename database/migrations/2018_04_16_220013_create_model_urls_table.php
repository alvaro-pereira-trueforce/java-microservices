<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModelUrlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('model_urls', function (Blueprint $table) {
            $table->uuid('uuid')->unique();
            $table->string('admin_ui');
            $table->string('pull_url');
            $table->string('channelback_url');
            $table->string('clickthrough_url');
            $table->string('healthcheck_url');
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
        Schema::dropIfExists('model_urls');
    }
}
