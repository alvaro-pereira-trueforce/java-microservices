<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTokensTelegramIntegration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('telegram_channels', function (Blueprint $table) {
            $table->string('instance_push_id')->after('token');
            $table->string('zendesk_access_token')->after('token');
            $table->string('token')->nullable()->change();
            $table->string('integration_name')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('telegram_channels', function (Blueprint $table) {
            $table->dropColumn('instance_push_id');
            $table->dropColumn('zendesk_access_token');
            $table->string('token')->nullable(false)->change();
            $table->string('integration_name')->nullable(false)->change();
        });
    }
}
