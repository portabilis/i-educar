<?php

use Illuminate\Database\Migrations\Migration;

return new class() extends Migration
{
    public function up()
    {
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.uniform_distributions SET SCHEMA public;');
    }

    public function down()
    {
        DB::unprepared('ALTER TABLE IF EXISTS public.uniform_distributions SET SCHEMA pmieducar;');
    }
};
