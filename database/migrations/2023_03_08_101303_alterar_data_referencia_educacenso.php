<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AlterarDataReferenciaEducacenso extends Migration
{
    public function up()
    {
        DB::table('pmieducar.instituicao')
            ->where('ativo', 1)
            ->update(['data_educacenso' => '2023-05-31']);
    }

    public function down()
    {
        DB::table('pmieducar.instituicao')
            ->where('ativo', 1)
            ->update(['data_educacenso' => '2022-05-25']);
    }
}
