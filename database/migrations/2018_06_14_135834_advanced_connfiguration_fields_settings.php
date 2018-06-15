<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AdvancedConnfigurationFieldsSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('channels_settings', function (Blueprint $table) {
            $table->string('ticket_type')->after('required_user_info')->nullable();
            $table->string('ticket_priority')->after('required_user_info')->nullable();
            $table->string('tags')->after('required_user_info')->nullable();
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
            $table->dropColumn('ticket_type');
            $table->dropColumn('ticket_priority');
            $table->dropColumn('tags');
        });
    }
}
