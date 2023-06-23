<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class() extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(
            'DROP FUNCTION IF EXISTS modules.frequencia_da_matricula(matricula integer, etapa character varying);'
        );

        DB::unprepared(
            file_get_contents(__DIR__ . '/../sqls/functions/modules.frequencia_da_matricula_2023-03-07.sql')
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared(
            'DROP FUNCTION IF EXISTS modules.frequencia_da_matricula(matricula integer, etapa character varying);'
        );

        DB::unprepared(
            file_get_contents(__DIR__ . '/../sqls/functions/modules.frequencia_da_matricula.sql')
        );
    }
};
