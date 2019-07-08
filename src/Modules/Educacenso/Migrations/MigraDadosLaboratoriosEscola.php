<?php

namespace iEducar\Modules\Educacenso\Migrations;

use iEducar\Modules\Educacenso\Model\Laboratorios;
use Illuminate\Support\Facades\DB;

class MigraDadosLaboratoriosEscola implements EducacensoMigrationInterface
{
    public static function execute()
    {
        $informatica = Laboratorios::INFORMATICA;
        $ciencias = Laboratorios::CIENCIAS;

        $sql = <<<SQL
                UPDATE pmieducar.escola
                    SET laboratorios = aux.array_laboratorios
                    FROM (
                           SELECT cod_escola,
                                  ARRAY_REMOVE(
                                      ARRAY [
                                        CASE WHEN dependencia_laboratorio_informatica = 1 THEN {$informatica} END,
                                        CASE WHEN dependencia_laboratorio_ciencias = 1 THEN {$ciencias} END
                                        ]
                                    , NULL) as array_laboratorios
                           FROM pmieducar.escola
                         ) AS aux
                    WHERE escola.cod_escola = aux.cod_escola
                    AND aux.array_laboratorios <> '{}'
SQL;
        DB::statement($sql);
    }
}