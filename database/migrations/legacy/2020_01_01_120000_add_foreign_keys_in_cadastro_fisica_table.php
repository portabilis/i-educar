<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInCadastroFisicaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cadastro.fisica', function (Blueprint $table) {
            $table->foreign('idpes_rev')
                ->references('idpes')
                ->on('cadastro.pessoa')
                ->onDelete('set null');

            $table->foreign('idpes_cad')
                ->references('idpes')
                ->on('cadastro.pessoa')
                ->onDelete('set null');

            $table->foreign('idpes_responsavel')
                ->references('idpes')
                ->on('cadastro.pessoa')
                ->onDelete('set null');

            $table->foreign('idpes_pai')
                ->references('idpes')
                ->on('cadastro.pessoa')
                ->onDelete('set null');

            $table->foreign('idpes_mae')
                ->references('idpes')
                ->on('cadastro.pessoa')
                ->onDelete('set null');

            $table->foreign('idpes_con')
                ->references('idpes')
                ->on('cadastro.pessoa')
                ->onDelete('set null');

            $table->foreign('idpes')
                ->references('idpes')
                ->on('cadastro.pessoa')
                ->onDelete('restrict');

            $table->foreign('idocup')
                ->references('idocup')
                ->on('cadastro.ocupacao');

            $table->foreign('ideciv')
                ->references('ideciv')
                ->on('cadastro.estado_civil');

            $table->foreign('idesco')
                ->references('idesco')
                ->on('cadastro.escolaridade');

            $table->foreign('ref_cod_religiao')
                ->references('cod_religiao')
                ->on('pmieducar.religiao');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cadastro.fisica', function (Blueprint $table) {
            $table->dropForeign(['idpes_rev']);
            $table->dropForeign(['idpes_cad']);
            $table->dropForeign(['idpes_responsavel']);
            $table->dropForeign(['idpes_pai']);
            $table->dropForeign(['idpes_mae']);
            $table->dropForeign(['idpes_con']);
            $table->dropForeign(['idpes']);
            $table->dropForeign(['idocup']);
            $table->dropForeign(['ideciv']);
            $table->dropForeign(['idesco']);
            $table->dropForeign(['ref_cod_religiao']);
        });
    }
}
