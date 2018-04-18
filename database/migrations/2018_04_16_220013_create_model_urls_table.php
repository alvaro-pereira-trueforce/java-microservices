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
        Schema::create('urls', function (Blueprint $table) {
            $table->primary('uuid');
            $table->uuid('uuid')->unique();
            $table->string('manifest_uuid');
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
        Schema::dropIfExists('urls');
    }
}