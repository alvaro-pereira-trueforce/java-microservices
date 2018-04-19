<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyManifestAndUrlTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('manifests', function (Blueprint $table) {
            $table->string('push_client_id')->after('id');
        });

        Schema::table('urls', function (Blueprint $table) {
            $table->string('about_url')->after('pull_url');
            $table->string('dashboard_url')->after('pull_url');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('manifests', function (Blueprint $table) {
            $table->dropColumn('push_client_id');
        });

        Schema::table('urls', function (Blueprint $table) {
            $table->dropColumn('about_url');
            $table->dropColumn('dashboard_url');
        });
    }
}
