<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CorrigeFuncaoDoServidorDisciplinas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('
            UPDATE pmieducar.servidor_disciplina
            SET ref_cod_funcao = (
                SELECT cod_servidor_funcao
                FROM pmieducar.servidor_funcao
                WHERE servidor_funcao.ref_cod_servidor = servidor_disciplina.ref_cod_servidor
                LIMIT 1
            )
            WHERE true
            AND NOT EXISTS (
                SELECT 1
                FROM pmieducar.servidor_funcao
                WHERE servidor_funcao.cod_servidor_funcao = servidor_disciplina.ref_cod_funcao
                AND servidor_funcao.ref_cod_servidor = servidor_disciplina.ref_cod_servidor
            );
        ');
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
