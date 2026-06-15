<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddFunctionRelatorioExibeAlunoConformeParametroAlunosDiferenciados extends Migration
{
    public function up(): void
    {
        $this->down();

        DB::unprepared(
            file_get_contents(database_path('sqls/functions/relatorio.exibe_aluno_conforme_parametro_alunos_diferenciados.sql'))
        );
    }

    public function down(): void
    {
        DB::unprepared(
            'DROP FUNCTION IF EXISTS relatorio.exibe_aluno_conforme_parametro_alunos_diferenciados(codigo_aluno integer, alunos_diferenciados integer);'
        );
    }
}
