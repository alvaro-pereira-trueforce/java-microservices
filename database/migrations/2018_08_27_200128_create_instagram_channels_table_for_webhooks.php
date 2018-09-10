<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInstagramChannelsTableForWebhooks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('instagram_channels', function (Blueprint $table) {
            $table->primary('uuid');
            $table->uuid('uuid')->unique();
            $table->string('integration_name');
            $table->string('instagram_id')->unique();
            $table->string('page_id');
            $table->string('subdomain');
            $table->string('instance_push_id',255);
            $table->string('zendesk_access_token', 255);
            $table->string('access_token', 255);
            $table->string('page_access_token', 255);
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
        Schema::dropIfExists('instagram_channels');
    }
}
