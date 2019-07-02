<?php

namespace iEducar\Modules\Educacenso\Migrations;

use Illuminate\Support\Facades\DB;

class MigraDadosCartorio implements EducacensoMigrationInterface
{
    public static function execute()
    {
        $sql = <<<'SQL'
                    UPDATE cadastro.documento
                    SET cartorio_cert_civil = aux.id_cartorio || ' - ' || aux.descricao
                    FROM (
                           SELECT id,
                                  descricao,
                                  id_cartorio
                           FROM cadastro.codigo_cartorio_inep
                    
                         ) aux
                    WHERE aux.id = cadastro.documento.cartorio_cert_civil_inep
                    AND cartorio_cert_civil IS NULL
SQL;


        DB::statement($sql);
    }
}