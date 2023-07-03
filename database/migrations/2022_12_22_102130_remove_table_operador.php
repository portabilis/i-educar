<?php

use Illuminate\Database\Migrations\Migration;

return new class() extends Migration
{
    public function up()
    {
        DB::unprepared('DROP TABLE IF EXISTS pmieducar.operador;');
        DB::unprepared('DROP SEQUENCE IF EXISTS pmieducar.operador_cod_operador_seq;');
    }
};
