<?php

namespace iEducar\Modules\Educacenso\Migrations;

use Illuminate\Support\Facades\DB;

class MigraTipoAtendimentoDaTurmaEducacenso implements EducacensoMigrationInterface
{
    public static function execute()
    {
        DB::table('pmieducar.turma')
            ->where('tipo_atendimento', 2)
            ->update([
                'tipo_atendimento' => null,
                'local_funcionamento_diferenciado' => 2,
            ]);

        DB::table('pmieducar.turma')
            ->where('tipo_atendimento', 3)
            ->update([
                'tipo_atendimento' => null,
                'local_funcionamento_diferenciado' => 3,
            ]);

        DB::table('pmieducar.turma')
            ->where('tipo_atendimento', 1)
            ->update([
                'tipo_atendimento' => null,
            ]);
    }
}