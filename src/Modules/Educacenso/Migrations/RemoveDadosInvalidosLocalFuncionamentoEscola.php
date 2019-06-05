<?php

namespace iEducar\Modules\Educacenso\Migrations;

use Illuminate\Support\Facades\DB;

class RemoveDadosInvalidosLocalFuncionamentoEscola
{
    public static function execute()
    {
        DB::table('pmieducar.escola')
            ->whereIn('local_funcionamento', [4,5,6])
            ->update(['local_funcionamento' => null]);
    }
}