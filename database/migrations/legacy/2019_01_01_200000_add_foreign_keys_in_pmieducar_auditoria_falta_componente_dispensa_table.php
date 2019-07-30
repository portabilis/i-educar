<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInPmieducarAuditoriaFaltaComponenteDispensaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.auditoria_falta_componente_dispensa', function (Blueprint $table) {
            $table->foreign('ref_cod_matricula')
               ->references('cod_matricula')
               ->on('pmieducar.matricula');

            $table->foreign('ref_cod_componente_curricular')
               ->references('id')
               ->on('modules.componente_curricular');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pmieducar.auditoria_falta_componente_dispensa', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_matricula']);
            $table->dropForeign(['ref_cod_componente_curricular']);
        });
    }
}
