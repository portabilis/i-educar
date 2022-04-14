<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInPmieducarServidorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.servidor', function (Blueprint $table) {
            $table->foreign('ref_idesco')
                ->references('idesco')
                ->on('cadastro.escolaridade')
                ->onUpdate('restrict')
                ->onDelete('restrict');

            $table->foreign('ref_cod_subnivel')
                ->references('cod_subnivel')
                ->on('pmieducar.subnivel')
                ->onUpdate('restrict')
                ->onDelete('restrict');

            $table->foreign('ref_cod_instituicao')
                ->references('cod_instituicao')
                ->on('pmieducar.instituicao')
                ->onUpdate('restrict')
                ->onDelete('restrict');

            $table->foreign('instituicao_curso_superior_3')
                ->references('id')
                ->on('modules.educacenso_ies');

            $table->foreign('instituicao_curso_superior_2')
                ->references('id')
                ->on('modules.educacenso_ies');

            $table->foreign('instituicao_curso_superior_1')
                ->references('id')
                ->on('modules.educacenso_ies');

            $table->foreign('cod_servidor')
                ->references('idpes')
                ->on('cadastro.pessoa');

            $table->foreign('codigo_curso_superior_3')
                ->references('id')
                ->on('modules.educacenso_curso_superior')
                ->onDelete('set null');

            $table->foreign('codigo_curso_superior_2')
                ->references('id')
                ->on('modules.educacenso_curso_superior')
                ->onDelete('set null');

            $table->foreign('codigo_curso_superior_1')
                ->references('id')
                ->on('modules.educacenso_curso_superior')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pmieducar.servidor', function (Blueprint $table) {
            $table->dropForeign(['ref_idesco']);
            $table->dropForeign(['ref_cod_subnivel']);
            $table->dropForeign(['ref_cod_instituicao']);
            $table->dropForeign(['instituicao_curso_superior_3']);
            $table->dropForeign(['instituicao_curso_superior_2']);
            $table->dropForeign(['instituicao_curso_superior_1']);
            $table->dropForeign(['cod_servidor']);
            $table->dropForeign(['codigo_curso_superior_3']);
            $table->dropForeign(['codigo_curso_superior_2']);
            $table->dropForeign(['codigo_curso_superior_1']);
        });
    }
}
