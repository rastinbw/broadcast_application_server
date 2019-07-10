<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToUstudentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ustudents', function (Blueprint $table) {
            $table->integer('field_id')->nullable();
            $table->integer('gender')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ustudents', function (Blueprint $table) {
            $table->dropColumn('field_id');
            $table->dropColumn('gender');
        });
    }
}
