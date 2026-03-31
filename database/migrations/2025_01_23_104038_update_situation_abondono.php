<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    use AsView;

    public function up()
    {
        DB::unprepared(
            file_get_contents(__DIR__ . '/../sqls/functions/relatorio.get_situacao_componente_2025-01-23.sql')
        );

        $this->createView('relatorio.view_situacao_relatorios', '2025-01-23');
    }

    public function down()
    {
        $this->createView('relatorio.view_situacao_relatorios', '2020-04-06');

        DB::unprepared(
            file_get_contents(__DIR__ . '/../sqls/functions/relatorio.get_situacao_componente_2020-01-01.sql')
        );
    }
};
