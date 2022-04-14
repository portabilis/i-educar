<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInPmieducarSerieTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.serie', function (Blueprint $table) {
            $table->foreign('regra_avaliacao_id')
                ->references('id')
                ->on('modules.regra_avaliacao')
                ->onUpdate('restrict')
                ->onDelete('restrict');

            $table->foreign('regra_avaliacao_diferenciada_id')
                ->references('id')
                ->on('modules.regra_avaliacao')
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
        Schema::table('pmieducar.serie', function (Blueprint $table) {
            $table->dropForeign(['regra_avaliacao_id']);
            $table->dropForeign(['regra_avaliacao_diferenciada_id']);
            $table->dropForeign(['ref_cod_curso']);
        });
    }
}
