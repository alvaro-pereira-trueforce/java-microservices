<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGroupsConfiguration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('channels_settings', function (Blueprint $table) {
            $table->boolean('tickets_by_group')->default(false)->after('required_user_info');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('channels_settings', function (Blueprint $table) {
            $table->dropColumn('tickets_by_group');
        });
    }
}
