<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysInLogUnificationOldDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('log_unification_old_data', function (Blueprint $table) {
            $table->foreign('unification_id')->on('log_unifications')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('log_unification_old_data', function (Blueprint $table) {
            $table->dropForeign(['unification_id']);
        });
    }
}
