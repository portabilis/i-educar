<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInPmieducarServidorCursoMinistraTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.servidor_curso_ministra', function (Blueprint $table) {
            $table->foreign(['ref_cod_servidor', 'ref_ref_cod_instituicao'])
                ->references(['cod_servidor', 'ref_cod_instituicao'])
                ->on('pmieducar.servidor')
                ->onUpdate('restrict')
                ->onDelete('restrict');

            $table->foreign('ref_cod_curso')
                ->references('cod_curso')
                ->on('pmieducar.curso')
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
        Schema::table('pmieducar.servidor_curso_ministra', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_servidor', 'ref_ref_cod_instituicao']);
            $table->dropForeign(['ref_cod_curso']);
        });
    }
}
