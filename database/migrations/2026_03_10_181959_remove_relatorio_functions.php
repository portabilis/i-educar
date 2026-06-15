<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('DROP FUNCTION IF EXISTS relatorio.get_ddd_escola');
        DB::unprepared('DROP FUNCTION IF EXISTS relatorio.get_media_geral_turma');
        DB::unprepared('DROP FUNCTION IF EXISTS relatorio.get_media_turma');
        DB::unprepared('DROP FUNCTION IF EXISTS relatorio.get_nacionalidade');
        DB::unprepared('DROP FUNCTION IF EXISTS relatorio.get_nota_exame');
        DB::unprepared('DROP FUNCTION IF EXISTS relatorio.get_qtde_alunos');
        DB::unprepared('DROP FUNCTION IF EXISTS relatorio.get_situacao_historico_abreviado');
        DB::unprepared('DROP FUNCTION IF EXISTS relatorio.get_situacao_historico');
        DB::unprepared('DROP FUNCTION IF EXISTS relatorio.get_total_falta_componente');
        DB::unprepared('DROP FUNCTION IF EXISTS relatorio.prioridade_historico');
        DB::unprepared('DROP FUNCTION IF EXISTS relatorio.get_max_sequencial_matricula');
        DB::unprepared('DROP FUNCTION IF EXISTS relatorio.get_pai_aluno');
        DB::unprepared('DROP FUNCTION IF EXISTS relatorio.get_telefone_escola');
        DB::unprepared('DROP FUNCTION IF EXISTS relatorio.get_total_geral_falta_componente');
        DB::unprepared('DROP FUNCTION IF EXISTS relatorio.get_mae_aluno');
    }
};
