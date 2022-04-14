<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInPmieducarReservaVagaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.reserva_vaga', function (Blueprint $table) {
            $table->foreign(['ref_ref_cod_serie', 'ref_ref_cod_escola'])
                ->references(['ref_cod_serie', 'ref_cod_escola'])
                ->on('pmieducar.escola_serie')
                ->onUpdate('restrict')
                ->onDelete('restrict');

            $table->foreign('ref_cod_aluno')
                ->references('cod_aluno')
                ->on('pmieducar.aluno')
                ->onUpdate('restrict')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pmieducar.reserva_vaga', function (Blueprint $table) {
            $table->dropForeign(['ref_ref_cod_serie', 'ref_ref_cod_escola']);
            $table->dropForeign(['ref_cod_aluno']);
        });
    }
}
