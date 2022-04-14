<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInPmieducarInfraPredioComodoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.infra_predio_comodo', function (Blueprint $table) {
            $table->foreign('ref_cod_infra_predio')
                ->references('cod_infra_predio')
                ->on('pmieducar.infra_predio')
                ->onUpdate('restrict')
                ->onDelete('restrict');

            $table->foreign('ref_cod_infra_comodo_funcao')
                ->references('cod_infra_comodo_funcao')
                ->on('pmieducar.infra_comodo_funcao')
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
        Schema::table('pmieducar.infra_predio_comodo', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_infra_predio']);
            $table->dropForeign(['ref_cod_infra_comodo_funcao']);
        });
    }
}
