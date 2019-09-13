<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddFunctionModulesFrequenciaEtapaPadraoAnoEscolarUm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(
            file_get_contents(__DIR__ . '/../../sqls/functions/modules.frequencia_etapa_padrao_ano_escolar_um.sql')
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared(
            'DROP FUNCTION modules.frequencia_etapa_padrao_ano_escolar_um(cod_matricula_aluno integer, cod_etapa integer, id_componente_curricular integer);'
        );
    }
}
