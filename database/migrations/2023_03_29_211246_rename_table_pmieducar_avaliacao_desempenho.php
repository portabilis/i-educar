<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class() extends Migration
{
    public function up()
    {
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.avaliacao_desempenho RENAME TO performance_evaluations;');
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.performance_evaluations SET SCHEMA public;');
    }

    public function down()
    {
        DB::unprepared('ALTER TABLE IF EXISTS public.performance_evaluations SET SCHEMA pmieducar;');
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.performance_evaluations RENAME TO avaliacao_desempenho;');
    }
};
