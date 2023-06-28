<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class() extends Migration
{
    public function up()
    {
        DB::unprepared('DROP TABLE IF EXISTS pmieducar.habilitacao;');
        DB::unprepared('DROP SEQUENCE IF EXISTS pmieducar.habilitacao_cod_habilitacao_seq;');
    }
};
