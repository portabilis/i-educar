<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnPmieducarMatriculaOcorrenciaDisciplinarTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.matricula_ocorrencia_disciplinar', function (Blueprint $table) {
            $table->foreign('ref_usuario_exc')
               ->references('cod_usuario')
               ->on('pmieducar.usuario')
               ->onUpdate('restrict')
               ->onDelete('restrict');

            $table->foreign('ref_usuario_cad')
               ->references('cod_usuario')
               ->on('pmieducar.usuario')
               ->onUpdate('restrict')
               ->onDelete('restrict');

            $table->foreign('ref_cod_matricula')
               ->references('cod_matricula')
               ->on('pmieducar.matricula')
               ->onUpdate('restrict')
               ->onDelete('restrict');

            $table->foreign('ref_cod_tipo_ocorrencia_disciplinar')
               ->references('cod_tipo_ocorrencia_disciplinar')
               ->on('pmieducar.tipo_ocorrencia_disciplinar')
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
        Schema::table('pmieducar.matricula_ocorrencia_disciplinar', function (Blueprint $table) {
            $table->dropForeign(['ref_usuario_exc']);
            $table->dropForeign(['ref_usuario_cad']);
            $table->dropForeign(['ref_cod_matricula']);
            $table->dropForeign(['ref_cod_tipo_ocorrencia_disciplinar']);
        });
    }
}
