<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class() extends Migration
{
    public function up()
    {
        DB::unprepared('ALTER TABLE IF EXISTS cadastro.fisica DROP CONSTRAINT IF EXISTS fk_fisica_ocupacao;');
        DB::unprepared('ALTER TABLE IF EXISTS cadastro.fisica DROP COLUMN IF EXISTS idocup;');
        DB::unprepared('DROP TABLE IF EXISTS cadastro.ocupacao;');
    }
};
