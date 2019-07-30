<?php

namespace iEducar\Modules\Educacenso\Migrations;

use iEducar\Modules\Educacenso\Model\AreasExternas;
use Illuminate\Support\Facades\DB;

class MigraDadosAreasExternasEscola implements EducacensoMigrationInterface
{
    public static function execute()
    {
        $quadraCoberta = AreasExternas::QUADRA_COBERTA;
        $quadraDescoberta = AreasExternas::QUADRA_DESCOBERTA;
        $patioCoberto = AreasExternas::PATIO_COBERTO;
        $patioDescoberto = AreasExternas::PATIO_DESCOBERTO;
        $parqueInfantil = AreasExternas::PARQUE_INFANTIL;
        $areaVerde = AreasExternas::AREA_VERDE;

        $sql = <<<SQL
                UPDATE pmieducar.escola
                    SET areas_externas = aux.array_areas_externas
                    FROM (
                           SELECT cod_escola,
                                  ARRAY_REMOVE(
                                      ARRAY [
                                        CASE WHEN dependencia_quadra_coberta = 1 THEN {$quadraCoberta} END,
                                        CASE WHEN dependencia_quadra_descoberta = 1 THEN {$quadraDescoberta} END,
                                        CASE WHEN dependencia_patio_coberto = 1 THEN {$patioCoberto} END,
                                        CASE WHEN dependencia_patio_descoberto = 1 THEN {$patioDescoberto} END,
                                        CASE WHEN dependencia_parque_infantil = 1 THEN {$parqueInfantil} END,
                                        CASE WHEN dependencia_area_verde = 1 THEN {$areaVerde} END
                                        ]
                                    , NULL) as array_areas_externas
                           FROM pmieducar.escola
                         ) AS aux
                    WHERE escola.cod_escola = aux.cod_escola
                    AND aux.array_areas_externas <> '{}'
SQL;
        DB::statement($sql);
    }
}