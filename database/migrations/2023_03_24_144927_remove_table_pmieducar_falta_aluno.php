<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class() extends Migration
{
    public function up()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS pmieducarfalta_aluno_audit ON pmieducar.falta_aluno;');
        DB::unprepared('DROP TABLE IF EXISTS pmieducar.falta_aluno;');
        DB::unprepared('DROP SEQUENCE IF EXISTS pmieducar.falta_aluno_cod_falta_aluno_seq;');
    }
};
