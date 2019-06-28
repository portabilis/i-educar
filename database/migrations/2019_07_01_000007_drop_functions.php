<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class DropFunctions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(
            '
                SET search_path = public, pg_catalog;
                
                DROP FUNCTION fcn_aft_fisica();
                
                DROP FUNCTION fcn_aft_fisica_cpf_provisorio();
                
                DROP FUNCTION fcn_aft_fisica_provisorio();
                
                SET search_path = modules, pg_catalog;
                
                DROP FUNCTION corrige_sequencial_historico();
                
                SET search_path = pmieducar, pg_catalog;
                
                DROP FUNCTION fcn_aft_update();
                
                SET search_path = relatorio, pg_catalog;
                
                DROP FUNCTION get_ddd_escola(integer);
                
                DROP FUNCTION get_dias_letivos_da_turma_por_etapa(v_turma integer, v_etapa integer);
                
                DROP FUNCTION get_mae_aluno(integer);
                
                DROP FUNCTION get_media_geral_turma(turma_i integer, componente_i integer);
                
                DROP FUNCTION get_media_recuperacao_semestral(matricula integer, componente integer);
                
                DROP FUNCTION get_media_turma(turma_i integer, componente_i integer, etapa_i integer);
                
                DROP FUNCTION get_nacionalidade(nacionalidade_id numeric);
                
                DROP FUNCTION get_qtde_alunos_situacao(ano integer, instituicao integer, escola integer, curso integer, serie integer, turma integer, situacao integer, bairro integer, sexo character, idadeini integer, idadefim integer);
                
                DROP FUNCTION retorna_situacao_matricula_componente(cod_situacao_matricula numeric, cod_situacao_componente numeric);
                
                SET search_path = public, pg_catalog;
            '
        );
    }
}
