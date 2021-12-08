<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBNCCSerieComponenteCurricularTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modules.bncc_serie_componente_curricular', function (Blueprint $table) {
            $table->id();
            $table->integer('bncc_id');
            $table->integer('serie_id');
            $table->integer('componente_curricular_id')->nullable();

            $table->foreign('bncc_id')
                ->references('id')
                ->on('modules.bncc')
                ->onDelete('cascade');

            $table->foreign('serie_id')
                ->references('cod_serie')
                ->on('pmieducar.serie')
                ->onDelete('cascade');
            
            $table->foreign('componente_curricular_id')
                ->references('id')
                ->on('modules.componente_curricular')
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
        Schema::dropIfExists('modules.bncc_serie_componente_curricular');
    }
}
