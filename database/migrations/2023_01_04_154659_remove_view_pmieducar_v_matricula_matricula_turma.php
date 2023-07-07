<?php

use Illuminate\Database\Migrations\Migration;

return new class() extends Migration
{
    public function up()
    {
        DB::unprepared(
            'DROP VIEW IF EXISTS pmieducar.v_matricula_matricula_turma;'
        );
    }
};
