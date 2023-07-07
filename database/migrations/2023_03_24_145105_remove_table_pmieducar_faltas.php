<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class() extends Migration
{
    public function up()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS pmieducarfaltas_audit ON pmieducar.faltas;');
        DB::unprepared('DROP TABLE IF EXISTS pmieducar.faltas;');
        DB::unprepared('DROP SEQUENCE IF EXISTS pmieducar.faltas_sequencial_seq;');
    }
};
