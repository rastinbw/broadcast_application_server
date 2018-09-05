<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePostTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->Text('title')->nullable()->change();
            $table->Text('preview_content')->nullable()->change();;
            $table->mediumText('content')->nullable()->change();;
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('title')->nullable()->change();
            $table->string('preview_content')->nullable()->change();;
            $table->Text('content')->nullable()->change();;
        });
    }
}
