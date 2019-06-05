<?php

namespace iEducar\Modules\Educacenso\Migrations;

use iEducar\Modules\Educacenso\Model\Equipamentos;
use Illuminate\Support\Facades\DB;

class MigraDadosEquipamentosEscola implements EducacensoMigrationInterface
{
    public static function execute()
    {
        $computadores = Equipamentos::COMPUTADORES;
        $impressoras = Equipamentos::IMPRESSORAS;
        $multifuncionais = Equipamentos::IMPRESSORAS_MULTIFUNCIONAIS;
        $copiadora = Equipamentos::COPIADORA;
        $antenaParabolica = Equipamentos::ANTENA_PARABOLICA;

        $sql = <<<SQL
                UPDATE pmieducar.escola
                    SET equipamentos = ARRAY(SELECT DISTINCT UNNEST(aux.array_equipamentos))
                    FROM (
                           SELECT cod_escola,
                                  ARRAY_REMOVE(
                                      ARRAY [
                                        CASE WHEN computadores_administrativo > 0 THEN {$computadores} END,
                                        CASE WHEN computadores_alunos > 0 THEN {$computadores} END,
                                        CASE WHEN computadores > 0 THEN {$computadores} END,
                                        CASE WHEN impressoras > 0 THEN {$impressoras} END,
                                        CASE WHEN impressoras_multifuncionais > 0 THEN {$multifuncionais} END,
                                        CASE WHEN copiadoras > 0 THEN {$copiadora} END,
                                        CASE WHEN antenas_parabolicas > 0 THEN {$antenaParabolica} END
                                        ]
                                    , NULL) as array_equipamentos
                           FROM pmieducar.escola
                         ) AS aux
                    WHERE escola.cod_escola = aux.cod_escola
                    AND aux.array_equipamentos <> '{}'
SQL;
        DB::statement($sql);
    }
}