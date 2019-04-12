<?php

use iEducar\Modules\Educacenso\Model\SalasAtividades;
use Illuminate\Database\Migrations\Migration;

class MigraDadosSalasAtividadesEscola extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $leitura = SalasAtividades::LEITURA;
        $recursosAee = SalasAtividades::RECURSOS_AEE;

        $sql = <<<SQL
                UPDATE pmieducar.escola
                    SET salas_atividades = aux.array_salas_atividades
                    FROM (
                           SELECT cod_escola,
                                  ARRAY_REMOVE(
                                      ARRAY [
                                        CASE WHEN dependencia_sala_leitura = 1 THEN {$leitura} END,
                                        CASE WHEN dependencia_sala_aee = 1 THEN {$recursosAee} END
                                        ]
                                    , NULL) as array_salas_atividades
                           FROM pmieducar.escola
                         ) AS aux
                    WHERE escola.cod_escola = aux.cod_escola
                    AND aux.array_salas_atividades <> '{}'
SQL;
        DB::statement($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
