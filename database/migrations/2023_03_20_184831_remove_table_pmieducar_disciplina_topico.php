<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class() extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP TABLE IF EXISTS pmieducar.disciplina_topico;');
        DB::unprepared('DROP SEQUENCE IF EXISTS pmieducar.disciplina_topico_cod_disciplina_topico_seq');
    }
};
