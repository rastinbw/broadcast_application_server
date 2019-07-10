<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('fire_base_server_key')->nullable();
            $table->string('apk_download_link')->nullable();
            $table->string('last_version')->nullable();
            $table->dropColumn('fire_base_api_key');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('fire_base_server_key');
            $table->dropColumn('apk_download_link');
            $table->dropColumn('last_version');
            $table->string('fire_base_api_key')->nullable();
        });
    }
}
