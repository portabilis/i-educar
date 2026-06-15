<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddFunctionRelatorioGetSituacaoComponente extends Migration
{
    public function up(): void
    {
        $this->down();

        DB::unprepared(
            file_get_contents(database_path('sqls/functions/relatorio.get_situacao_componente.sql'))
        );
    }

    public function down(): void
    {
        DB::unprepared(
            'DROP FUNCTION IF EXISTS relatorio.get_situacao_componente(cod_situacao numeric);'
        );
    }
}
