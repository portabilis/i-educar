<?php

namespace iEducar\Modules\Educacenso\Migrations;

use iEducar\Modules\Educacenso\Model\Dormitorios;
use Illuminate\Support\Facades\DB;

class MigraDadosDormitorioEscola implements EducacensoMigrationInterface
{
    public static function execute()
    {
        $aluno = Dormitorios::ALUNO;
        $professor = Dormitorios::PROFESSOR;

        $sql = <<<SQL
                UPDATE pmieducar.escola
                    SET dormitorios = aux.array_dormitorios
                    FROM (
                           SELECT cod_escola,
                                  ARRAY_REMOVE(
                                      ARRAY [
                                        CASE WHEN dependencia_alojamento_aluno = 1 THEN {$aluno} END,
                                        CASE WHEN dependencia_alojamento_professor = 1 THEN {$professor} END
                                        ]
                                    , NULL) as array_dormitorios
                           FROM pmieducar.escola
                         ) AS aux
                    WHERE escola.cod_escola = aux.cod_escola
                    AND aux.array_dormitorios <> '{}'
SQL;
        DB::statement($sql);
    }
}