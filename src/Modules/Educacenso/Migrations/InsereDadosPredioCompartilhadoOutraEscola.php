<?php

namespace iEducar\Modules\Educacenso\Migrations;

use Illuminate\Support\Facades\DB;

class InsereDadosPredioCompartilhadoOutraEscola implements EducacensoMigrationInterface
{
    public static function execute()
    {
        $sql = <<<'SQL'
          UPDATE pmieducar.escola
             SET predio_compartilhado_outra_escola = aux.predio_compartilhado_outra_escola
             FROM (
                   SELECT cod_escola,
                          CASE
                            WHEN (
                                codigo_inep_escola_compartilhada IS NULL AND
                                codigo_inep_escola_compartilhada2 IS NULL AND
                                codigo_inep_escola_compartilhada3 IS NULL AND
                                codigo_inep_escola_compartilhada4 IS NULL AND
                                codigo_inep_escola_compartilhada5 IS NULL AND
                                codigo_inep_escola_compartilhada6 IS NULL
                              ) THEN
                              0
                            ELSE
                              1
                            END as predio_compartilhado_outra_escola
                   FROM pmieducar.escola
                  ) AS aux
             WHERE 3 = ANY(escola.local_funcionamento)
               AND escola.cod_escola = aux.cod_escola
SQL;
        DB::statement($sql);
    }
}