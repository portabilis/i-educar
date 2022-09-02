<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SeriesBncc extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pmieducar.bncc_series', function (Blueprint $table) {
            $table->id();
            $table->integer('id_bncc');
            $table->integer('id_serie');
          
        });

        Schema::create('pmieducar.bncc_series', function (Blueprint $table) {
            $table->foreign('id_bncc')
                ->references('id')
                ->on('modules.bncc')
                ->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
