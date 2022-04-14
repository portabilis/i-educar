<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInPmieducarTransferenciaSolicitacaoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.transferencia_solicitacao', function (Blueprint $table) {
            $table->foreign('ref_cod_transferencia_tipo')
                ->references('cod_transferencia_tipo')
                ->on('pmieducar.transferencia_tipo')
                ->onUpdate('restrict')
                ->onDelete('restrict');

            $table->foreign('ref_cod_matricula_saida')
                ->references('cod_matricula')
                ->on('pmieducar.matricula')
                ->onUpdate('restrict')
                ->onDelete('restrict');

            $table->foreign('ref_cod_matricula_entrada')
                ->references('cod_matricula')
                ->on('pmieducar.matricula')
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
        Schema::table('pmieducar.transferencia_solicitacao', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_transferencia_tipo']);
            $table->dropForeign(['ref_cod_matricula_saida']);
            $table->dropForeign(['ref_cod_matricula_entrada']);
        });
    }
}
