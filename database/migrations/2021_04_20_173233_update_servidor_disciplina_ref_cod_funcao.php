<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateServidorDisciplinaRefCodFuncao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(
            'UPDATE  pmieducar.servidor_disciplina sd SET ref_cod_funcao =
                (
                    SELECT f.cod_funcao
                    FROM pmieducar.servidor_funcao sf
                    INNER JOIN pmieducar.funcao f
                    ON f.cod_funcao = sf.ref_cod_funcao
                    WHERE TRUE
                    AND f.professor = 1
                    AND f.ativo = 1
                    AND sf.ref_cod_servidor = sd.ref_cod_servidor
                    LIMIT 1
                )
            WHERE sd.ref_cod_funcao IS NULL;'
        );
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
