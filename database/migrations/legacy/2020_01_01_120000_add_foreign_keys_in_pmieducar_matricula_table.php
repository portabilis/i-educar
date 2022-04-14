<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInPmieducarMatriculaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.matricula', function (Blueprint $table) {
            $table->foreign('ref_ref_cod_serie')
                ->references('cod_serie')
                ->on('pmieducar.serie')
                ->onUpdate('restrict')
                ->onDelete('restrict');

            $table->foreign('ref_ref_cod_escola')
                ->references('cod_escola')
                ->on('pmieducar.escola')
                ->onUpdate('restrict')
                ->onDelete('restrict');

            $table->foreign('ref_cod_reserva_vaga')
                ->references('cod_reserva_vaga')
                ->on('pmieducar.reserva_vaga')
                ->onUpdate('restrict')
                ->onDelete('restrict');

            $table->foreign('ref_cod_curso')
                ->references('cod_curso')
                ->on('pmieducar.curso')
                ->onUpdate('restrict')
                ->onDelete('restrict');

            $table->foreign('ref_cod_aluno')
                ->references('cod_aluno')
                ->on('pmieducar.aluno')
                ->onUpdate('restrict')
                ->onDelete('restrict');

            $table->foreign('ref_cod_abandono_tipo')
                ->references('cod_abandono_tipo')
                ->on('pmieducar.abandono_tipo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pmieducar.matricula', function (Blueprint $table) {
            $table->dropForeign(['ref_ref_cod_serie']);
            $table->dropForeign(['ref_ref_cod_escola']);
            $table->dropForeign(['ref_cod_reserva_vaga']);
            $table->dropForeign(['ref_cod_curso']);
            $table->dropForeign(['ref_cod_aluno']);
            $table->dropForeign(['ref_cod_abandono_tipo']);
        });
    }
}
