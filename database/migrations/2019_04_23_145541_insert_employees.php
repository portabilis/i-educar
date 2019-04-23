<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class InsertEmployees extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<'SQL'
                INSERT INTO pmieducar.servidor (cod_servidor, ref_cod_instituicao, carga_horaria, data_cadastro, ativo)
                    SELECT DISTINCT employee_id,
                           (SELECT instituicao.cod_instituicao
                            FROM pmieducar.instituicao
                            WHERE ativo = 1
                            LIMIT 1),
                           0,
                           NOW(),
                           1
                         FROM school_managers
                    LEFT JOIN pmieducar.servidor ON servidor.cod_servidor = school_managers.employee_id
                    WHERE servidor.cod_servidor IS NULL
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
