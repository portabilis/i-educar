<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDatesReleasePeriodTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dates_release_period', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('start_data');
            $table->date('end_data');
            $table->integer('release_period_id');

            $table->foreign('release_period_id')->references('id')->on('release_period');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dates_release_period');
    }
}
