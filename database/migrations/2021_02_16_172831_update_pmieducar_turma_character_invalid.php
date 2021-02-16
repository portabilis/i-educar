<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdatePmieducarTurmaCharacterInvalid extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(
            '
            UPDATE pmieducar.turma SET nm_turma = REPLACE(nm_turma, \'°\', \'º\')
            WHERE cod_turma IN (
                SELECT cod_turma FROM pmieducar.turma
                WHERE turma.nm_turma  LIKE \'%\' || \'°\' || \'%\'
            )'
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
