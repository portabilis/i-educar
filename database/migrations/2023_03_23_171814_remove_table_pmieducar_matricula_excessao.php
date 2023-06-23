<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class() extends Migration
{
    public function up()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS pmieducarmatricula_excessao_audit ON pmieducar.matricula_excessao;');
        DB::unprepared('DROP TABLE IF EXISTS pmieducar.matricula_excessao;');
        DB::unprepared('DROP SEQUENCE IF EXISTS pmieducar.matricula_excessao_cod_aluno_excessao_seq;');
    }
};
