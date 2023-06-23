<?php

use Illuminate\Database\Migrations\Migration;

return new class() extends Migration
{
    public function up()
    {
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.distribuicao_uniforme RENAME TO uniform_distributions;');
    }

    public function down()
    {
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.uniform_distributions RENAME TO distribuicao_uniforme;');
    }
};
