<?php

namespace iEducar\Modules\Educacenso\Migrations;

use iEducar\Modules\Educacenso\Model\Banheiros;
use Illuminate\Support\Facades\DB;

class MigraDadosBanheirosEscola implements EducacensoMigrationInterface
{
    public static function execute()
    {
        $banheiro = Banheiros::BANHEIRO;
        $banheiroChuveiro = Banheiros::BANHEIRO_CHUVEIRO;
        $banheiroEducacaoInfantil = Banheiros::BANHEIRO_EDUCACAO_INFANTIL;
        $banheiroAcessivel = Banheiros::BANHEIRO_ACESSIVEL;

        $sql = <<<SQL
                UPDATE pmieducar.escola
                    SET banheiros = aux.array_banheiros
                    FROM (
                           SELECT cod_escola,
                                  ARRAY_REMOVE(
                                      ARRAY [
                                        CASE WHEN dependencia_banheiro_fora = 1 THEN {$banheiro} END,
                                        CASE WHEN dependencia_banheiro_dentro = 1 THEN {$banheiro} END,
                                        CASE WHEN dependencia_banheiro_chuveiro = 1 THEN {$banheiroChuveiro} END,
                                        CASE WHEN dependencia_banheiro_infantil = 1 THEN {$banheiroEducacaoInfantil} END,
                                        CASE WHEN dependencia_banheiro_deficiente = 1 THEN {$banheiroAcessivel} END
                                        ]
                                    , NULL) as array_banheiros
                           FROM pmieducar.escola
                         ) AS aux
                    WHERE escola.cod_escola = aux.cod_escola
                    AND aux.array_banheiros <> '{}'
SQL;
        DB::statement($sql);
    }
}