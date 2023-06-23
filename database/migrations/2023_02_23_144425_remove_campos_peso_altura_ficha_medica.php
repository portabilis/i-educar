<?php

use Illuminate\Database\Migrations\Migration;

return new class() extends Migration
{
    public function up()
    {
        DB::unprepared('ALTER TABLE IF EXISTS modules.ficha_medica_aluno DROP COLUMN IF EXISTS peso;');
        DB::unprepared('ALTER TABLE IF EXISTS modules.ficha_medica_aluno DROP COLUMN IF EXISTS altura;');
    }

    public function down()
    {
        DB::unprepared('ALTER TABLE IF EXISTS modules.ficha_medica_aluno ADD COLUMN altura character varying(4)');
        DB::unprepared('ALTER TABLE IF EXISTS modules.ficha_medica_aluno ADD COLUMN peso character varying(7)');
    }
};
