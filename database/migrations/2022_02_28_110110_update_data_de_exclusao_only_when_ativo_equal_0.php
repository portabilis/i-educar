<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateDataDeExclusaoOnlyWhenAtivoEqual0 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(
            'UPDATE pmieducar.turma SET data_exclusao = now(), visivel = FALSE
            WHERE turma.ativo = 0'
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
