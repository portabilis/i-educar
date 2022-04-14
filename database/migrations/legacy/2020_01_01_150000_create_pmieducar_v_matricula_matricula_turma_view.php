<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreatePmieducarVMatriculaMatriculaTurmaView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(
            'DROP VIEW IF EXISTS pmieducar.v_matricula_matricula_turma;'
        );

        DB::unprepared(
            file_get_contents(__DIR__ . '/../../sqls/views/pmieducar.v_matricula_matricula_turma.sql')
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
            'DROP VIEW IF EXISTS pmieducar.v_matricula_matricula_turma;'
        );
    }
}
