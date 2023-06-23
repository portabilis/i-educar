<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class() extends Migration
{
    public function up()
    {
        DB::unprepared('DROP TABLE IF EXISTS modules.docente_licenciatura;');
        DB::unprepared('DROP SEQUENCE IF EXISTS modules.docente_licenciatura_id_seq;');
        DB::unprepared('DROP TRIGGER IF EXISTS modulesdocente_licenciatura_audit ON modules.docente_licenciatura;');
    }
};
