<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInPmieducarQuadroHorarioHorariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.quadro_horario_horarios', function (Blueprint $table) {
            $table->foreign(['ref_servidor_substituto', 'ref_cod_instituicao_substituto'])
               ->references(['cod_servidor', 'ref_cod_instituicao'])
               ->on('pmieducar.servidor')
               ->onUpdate('restrict')
               ->onDelete('restrict');

            $table->foreign(['ref_servidor', 'ref_cod_instituicao_servidor'])
               ->references(['cod_servidor', 'ref_cod_instituicao'])
               ->on('pmieducar.servidor')
               ->onUpdate('restrict')
               ->onDelete('restrict');

            $table->foreign('ref_cod_quadro_horario')
               ->references('cod_quadro_horario')
               ->on('pmieducar.quadro_horario')
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
        Schema::table('pmieducar.quadro_horario_horarios', function (Blueprint $table) {
            $table->dropForeign(['ref_servidor_substituto', 'ref_cod_instituicao_substituto']);
            $table->dropForeign(['ref_servidor', 'ref_cod_instituicao_servidor']);
            $table->dropForeign(['ref_cod_quadro_horario']);
        });
    }
}
