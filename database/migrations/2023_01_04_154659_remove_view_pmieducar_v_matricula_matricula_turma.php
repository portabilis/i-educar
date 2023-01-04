<?php

use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up()
    {
        DB::unprepared(
            'DROP VIEW pmieducar.v_matricula_matricula_turma;'
        );
    }
};
