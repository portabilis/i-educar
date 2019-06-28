<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogUnificationOldDataTable extends Migration
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
