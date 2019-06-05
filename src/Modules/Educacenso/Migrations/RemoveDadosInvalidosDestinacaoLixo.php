<?php

namespace iEducar\Modules\Educacenso\Migrations;

use Illuminate\Support\Facades\DB;

class RemoveDadosInvalidosDestinacaoLixo implements EducacensoMigrationInterface
{
    public static function execute()
    {
        DB::statement('UPDATE pmieducar.escola SET destinacao_lixo = array_remove(destinacao_lixo, 4)');
        DB::statement('UPDATE pmieducar.escola SET destinacao_lixo = array_remove(destinacao_lixo, 6)');
    }
}