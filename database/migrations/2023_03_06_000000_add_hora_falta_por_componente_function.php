<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class() extends Migration
{
    public function up()
    {
        DB::unprepared(
            'DROP FUNCTION IF EXISTS modules.hora_falta_por_componente(cod_matricula_id integer, cod_disciplina_id integer);'
        );

        DB::unprepared(
            file_get_contents(__DIR__ . '/../sqls/functions/modules.hora_falta_por_componente.sql')
        );
    }

    public function down()
    {
        DB::unprepared(
            'DROP FUNCTION IF EXISTS modules.hora_falta_por_componente(cod_matricula_id integer, cod_disciplina_id integer);'
        );
    }
};
