<?php

namespace iEducar\Modules\Educacenso\Migrations;

use iEducar\Modules\Educacenso\Model\SalasAtividades;
use Illuminate\Support\Facades\DB;

class MigraDadosSalasAtividadesEscola implements EducacensoMigrationInterface
{
    public static function execute()
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
}