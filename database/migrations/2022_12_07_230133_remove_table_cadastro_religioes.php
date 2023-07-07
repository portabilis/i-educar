<?php

use Illuminate\Database\Migrations\Migration;

return new class() extends Migration
{
    public function up()
    {
        DB::unprepared('DROP TABLE IF EXISTS cadastro.religiao');
        DB::unprepared('DROP SEQUENCE IF EXISTS cadastro.religiao_cod_religiao_seq;');
    }
};
