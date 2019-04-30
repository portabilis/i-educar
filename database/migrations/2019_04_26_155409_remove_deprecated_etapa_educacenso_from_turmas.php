<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveDeprecatedEtapaEducacensoFromTurmas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('pmieducar.turma')
            ->where('ano', 2019)
            ->whereIn('etapa_educacenso', [4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 65])
            ->update([
                'etapa_educacenso' => null
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
