<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnPmieducarDisciplinaTopicoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.disciplina_topico', function (Blueprint $table) {
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
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pmieducar.disciplina_topico', function (Blueprint $table) {
            $table->dropForeign(['ref_usuario_exc']);
            $table->dropForeign(['ref_usuario_cad']);
        });
    }
}
