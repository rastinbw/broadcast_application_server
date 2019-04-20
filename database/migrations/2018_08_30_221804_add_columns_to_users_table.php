<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('post_time_limit')->nullable();
            $table->integer('message_log_time_limit')->nullable();
            $table->integer('student_count_limit')->nullable();
            $table->integer('media_count_limit')->nullable();
            $table->integer('program_count_limit')->nullable();
            $table->integer('staff_count_limit')->nullable();
            $table->date('activation_date')->nullable();
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
            $table->dropColumn('post_time_limit');
            $table->dropColumn('message_log_time_limit');
            $table->dropColumn('program_count_limit');
            $table->dropColumn('media_count_limit');
            $table->dropColumn('student_count_limit');
            $table->dropColumn('activation_date');
        });
    }
}
