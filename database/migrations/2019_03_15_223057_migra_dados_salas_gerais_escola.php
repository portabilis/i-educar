<?php

use iEducar\Modules\Educacenso\Model\SalasGerais;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class MigraDadosSalasGeraisEscola extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $salaDiretoria = SalasGerais::SALA_DIRETORIA;
        $salaSecretaria = SalasGerais::SALA_SECRETARIA;
        $salaProfessores = SalasGerais::SALA_PROFESSORES;
        $biblioteca = SalasGerais::BIBLIOTECA;
        $diretoria = SalasGerais::AUDITORIO;

        $sql = <<<SQL
                UPDATE pmieducar.escola
                    SET salas_gerais = aux.array_salas_gerais
                    FROM (
                           SELECT cod_escola,
                                  ARRAY_REMOVE(
                                      ARRAY [
                                        CASE WHEN dependencia_sala_diretoria = 1 THEN {$salaDiretoria} END,
                                        CASE WHEN dependencia_sala_secretaria = 1 THEN {$salaSecretaria} END,
                                        CASE WHEN dependencia_sala_professores = 1 THEN {$salaProfessores} END,
                                        CASE WHEN dependencia_biblioteca = 1 THEN {$biblioteca} END,
                                        CASE WHEN dependencia_auditorio = 1 THEN {$diretoria} END
                                        ]
                                    , NULL) as array_salas_gerais
                           FROM pmieducar.escola
                         ) AS aux
                    WHERE escola.cod_escola = aux.cod_escola
                    AND aux.array_salas_gerais <> '{}'
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
