<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateProgramTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->Text('title')->change();
            $table->Text('preview_content')->nullable()->change();
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
        Schema::table('programs', function (Blueprint $table) {
            $table->Text('title')->nullable()->change();
            $table->Text('preview_content')->nullable()->change();
            $table->Text('content')->nullable()->change();
        });

    }
}
