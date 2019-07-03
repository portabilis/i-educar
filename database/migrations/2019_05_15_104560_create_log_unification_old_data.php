<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogUnificationOldData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_unification_old_data', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('unification_id')->unsigned();
            $table->string('table');
            $table->json('keys');
            $table->json('old_data');

            $table->foreign('unification_id')->references('id')->on('log_unifications');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log_unification_old_data');
    }
}
