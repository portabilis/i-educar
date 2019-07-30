<?php

namespace iEducar\Modules\Educacenso\Migrations;

use iEducar\Modules\Educacenso\Model\SalasFuncionais;
use Illuminate\Support\Facades\DB;

class MigraDadosSalasFuncionaisEscola implements EducacensoMigrationInterface
{
    public static function execute()
    {
        $cozinha = SalasFuncionais::COZINHA;
        $refeitorio = SalasFuncionais::REFEITORIO;
        $despensa = SalasFuncionais::DESPENSA;
        $almoxarifado = SalasFuncionais::ALMOXARIFADO;

        $sql = <<<SQL
                UPDATE pmieducar.escola
                    SET salas_funcionais = aux.array_salas_funcionais
                    FROM (
                           SELECT cod_escola,
                                  ARRAY_REMOVE(
                                      ARRAY [
                                        CASE WHEN dependencia_cozinha = 1 THEN {$cozinha} END,
                                        CASE WHEN dependencia_refeitorio = 1 THEN {$refeitorio} END,
                                        CASE WHEN dependencia_dispensa = 1 THEN {$despensa} END,
                                        CASE WHEN dependencia_aumoxarifado = 1 THEN {$almoxarifado} END
                                        ]
                                    , NULL) as array_salas_funcionais
                           FROM pmieducar.escola
                         ) AS aux
                    WHERE escola.cod_escola = aux.cod_escola
                    AND aux.array_salas_funcionais <> '{}'
SQL;
        DB::statement($sql);
    }
}