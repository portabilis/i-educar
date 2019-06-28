<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class PrepareDatabase extends Migration
{
    /**
     * @see https://github.com/portabilis/i-educar/pull/576
     * @see https://github.com/portabilis/i-educar/pull/496
     * @see https://github.com/portabilis/i-educar/pull/497
     * @see https://github.com/portabilis/i-educar/pull/498
     * @see https://github.com/portabilis/i-educar/pull/507
     * @see https://github.com/portabilis/i-educar/pull/500
     * @see https://github.com/portabilis/i-educar/pull/501
     * @see https://github.com/portabilis/i-educar/pull/503
     * @see https://github.com/portabilis/i-educar/pull/499
     * @see https://github.com/portabilis/i-educar/pull/606
     */
    private function dropSchemas()
    {
        DB::unprepared('DROP SCHEMA IF EXISTS acesso CASCADE;');
        DB::unprepared('DROP SCHEMA IF EXISTS alimentos CASCADE;');
        DB::unprepared('DROP SCHEMA IF EXISTS consistenciacao CASCADE;');
        DB::unprepared('DROP SCHEMA IF EXISTS conv_functions CASCADE;');
        DB::unprepared('DROP SCHEMA IF EXISTS historico CASCADE;');
        DB::unprepared('DROP SCHEMA IF EXISTS pmiacoes CASCADE;');
        DB::unprepared('DROP SCHEMA IF EXISTS pmicontrolesis CASCADE;');
        DB::unprepared('DROP SCHEMA IF EXISTS pmidrh CASCADE;');
        DB::unprepared('DROP SCHEMA IF EXISTS pmiotopic CASCADE;');
        DB::unprepared('DROP SCHEMA IF EXISTS serieciasc CASCADE;');
    }

    /**
     * @see https://github.com/portabilis/i-educar/pull/601
     */
    private function dropFromPmieducar()
    {
        DB::unprepared('DROP TABLE IF EXISTS pmieducar.historico_educar CASCADE;');
    }

    /**
     * @see https://github.com/portabilis/i-educar/pull/509
     * @see https://github.com/portabilis/i-educar/pull/602
     * @see https://github.com/portabilis/i-educar/pull/607
     * @see https://github.com/portabilis/i-educar/pull/608
     * @see https://github.com/portabilis/i-educar/pull/609
     */
    private function dropFromPortal()
    {
        DB::unprepared('DROP TABLE IF EXISTS portal.agenda_pref CASCADE;');
        DB::unprepared('DROP TABLE IF EXISTS portal.compras_editais_editais CASCADE;');
        DB::unprepared('DROP TABLE IF EXISTS portal.compras_editais_editais_empresas CASCADE;');
        DB::unprepared('DROP TABLE IF EXISTS portal.compras_editais_empresa CASCADE;');
        DB::unprepared('DROP TABLE IF EXISTS portal.compras_final_pregao CASCADE;');
        DB::unprepared('DROP TABLE IF EXISTS portal.compras_funcionarios CASCADE;');
        DB::unprepared('DROP TABLE IF EXISTS portal.compras_licitacoes CASCADE;');
        DB::unprepared('DROP TABLE IF EXISTS portal.compras_modalidade CASCADE;');
        DB::unprepared('DROP TABLE IF EXISTS portal.compras_pregao_execucao CASCADE;');
        DB::unprepared('DROP TABLE IF EXISTS portal.compras_prestacao_contas CASCADE;');
        DB::unprepared('DROP TABLE IF EXISTS portal.foto_portal CASCADE;');
        DB::unprepared('DROP TABLE IF EXISTS portal.foto_secao CASCADE;');
        DB::unprepared('DROP TABLE IF EXISTS portal.imagem CASCADE;');
        DB::unprepared('DROP TABLE IF EXISTS portal.imagem_tipo CASCADE;');
        DB::unprepared('DROP TABLE IF EXISTS portal.intranet_segur_permissao_negada CASCADE;');
        DB::unprepared('DROP TABLE IF EXISTS portal.jor_arquivo CASCADE;');
        DB::unprepared('DROP TABLE IF EXISTS portal.jor_edicao CASCADE;');
        DB::unprepared('DROP TABLE IF EXISTS portal.mailling_email CASCADE;');
        DB::unprepared('DROP TABLE IF EXISTS portal.mailling_email_conteudo CASCADE;');
        DB::unprepared('DROP TABLE IF EXISTS portal.mailling_fila_envio CASCADE;');
        DB::unprepared('DROP TABLE IF EXISTS portal.mailling_grupo CASCADE;');
        DB::unprepared('DROP TABLE IF EXISTS portal.mailling_grupo_email CASCADE;');
        DB::unprepared('DROP TABLE IF EXISTS portal.mailling_historico CASCADE;');
        DB::unprepared('DROP TABLE IF EXISTS portal.menu_funcionario CASCADE;');
        DB::unprepared('DROP TABLE IF EXISTS portal.menu_menu CASCADE;');
        DB::unprepared('DROP TABLE IF EXISTS portal.menu_submenu CASCADE;');
        DB::unprepared('DROP TABLE IF EXISTS portal.not_portal CASCADE;');
        DB::unprepared('DROP TABLE IF EXISTS portal.not_portal_tipo CASCADE;');
        DB::unprepared('DROP TABLE IF EXISTS portal.not_tipo CASCADE;');
        DB::unprepared('DROP TABLE IF EXISTS portal.not_vinc_portal CASCADE;');
        DB::unprepared('DROP TABLE IF EXISTS portal.notificacao CASCADE;');
        DB::unprepared('DROP TABLE IF EXISTS portal.pessoa_atividade CASCADE;');
        DB::unprepared('DROP TABLE IF EXISTS portal.pessoa_fj CASCADE;');
        DB::unprepared('DROP TABLE IF EXISTS portal.pessoa_fj_pessoa_atividade CASCADE;');
        DB::unprepared('DROP TABLE IF EXISTS portal.pessoa_ramo_atividade CASCADE;');
        DB::unprepared('DROP TABLE IF EXISTS portal.portal_concurso CASCADE;');
        DB::unprepared('DROP TABLE IF EXISTS portal.sistema CASCADE;');
    }

    /**
     * @see https://github.com/portabilis/i-educar/pull/504
     * @see https://github.com/portabilis/i-educar/pull/505
     * @see https://github.com/portabilis/i-educar/pull/514
     * @see https://github.com/portabilis/i-educar/pull/515
     * @see https://github.com/portabilis/i-educar/pull/516
     */
    private function dropFromPublic()
    {
        DB::unprepared('DROP TABLE IF EXISTS public.changelog CASCADE;');
        DB::unprepared('DROP TABLE IF EXISTS public.phinxlog CASCADE;');
        DB::unprepared('DROP TABLE IF EXISTS public.portal_banner CASCADE;');
        DB::unprepared('DROP TABLE IF EXISTS public.pghero_query_stats CASCADE;');
        DB::unprepared('DROP TABLE IF EXISTS public.vila CASCADE;');
        DB::unprepared('DROP TABLE IF EXISTS public.bairro_regiao CASCADE;');
        DB::unprepared('DROP TABLE IF EXISTS public.regiao CASCADE;');
        DB::unprepared('DROP TABLE IF EXISTS public.logradouro_fonetico CASCADE;');
        DB::unprepared('DROP TABLE IF EXISTS public.setor CASCADE;');

        DB::unprepared('DROP EXTENSION IF EXISTS pg_stat_statements;');

        DB::unprepared('DROP FUNCTION IF EXISTS public.fcn_aft_logradouro_fonetiza() CASCADE;');
        DB::unprepared('DROP FUNCTION IF EXISTS public.fcn_bef_logradouro_fonetiza() CASCADE;');
        DB::unprepared('DROP FUNCTION IF EXISTS public.fcn_cons_log_fonetica(text, bigint) CASCADE;');
        DB::unprepared('DROP FUNCTION IF EXISTS public.fcn_delete_funcionario(integer, integer) CASCADE;');
        DB::unprepared('DROP FUNCTION IF EXISTS public.fcn_dia_util(date, date) CASCADE;');
        DB::unprepared('DROP FUNCTION IF EXISTS public.fcn_fonetiza_logr_geral() CASCADE;');
        DB::unprepared('DROP FUNCTION IF EXISTS public.fcn_insert_funcionario(integer, integer, integer, integer, character varying, character, integer, integer) CASCADE;');
        DB::unprepared('DROP FUNCTION IF EXISTS public.fcn_update_funcionario(numeric, integer, integer, character varying, character, integer, integer) CASCADE;');
        DB::unprepared('DROP FUNCTION IF EXISTS public.cria_distritos() CASCADE;');
        DB::unprepared('DROP FUNCTION IF EXISTS public.fcn_aft_pessoa_fonetiza() CASCADE;');
        DB::unprepared('DROP FUNCTION IF EXISTS public.fcn_bef_ins_fisica() CASCADE;');
        DB::unprepared('DROP FUNCTION IF EXISTS public.fcn_bef_ins_juridica() CASCADE;');
        DB::unprepared('DROP FUNCTION IF EXISTS public.fcn_bef_pessoa_fonetiza() CASCADE;');
        DB::unprepared('DROP FUNCTION IF EXISTS public.fcn_consulta_fonetica(text) CASCADE;');
        DB::unprepared('DROP FUNCTION IF EXISTS public.fcn_delete_endereco_externo(integer, integer) CASCADE;');
        DB::unprepared('DROP FUNCTION IF EXISTS public.fcn_insert_fisica_cpf(integer, text, character, integer, integer) CASCADE;');
        DB::unprepared('DROP FUNCTION IF EXISTS public.fcn_insert_fisica(integer, character varying, character varying, integer, integer, integer, integer, integer, integer, character varying, character varying, integer, integer, character varying, integer, character varying, integer, character varying, character varying, character varying, character varying, character varying, character, integer, integer) CASCADE;');
        DB::unprepared('DROP FUNCTION IF EXISTS public.fcn_insert_fone_pessoa(integer, integer, integer, integer, character, integer, integer) CASCADE;');
        DB::unprepared('DROP FUNCTION IF EXISTS public.fcn_insert_juridica(integer, character varying, character varying, character varying, character, integer, integer) CASCADE;');
        DB::unprepared('DROP FUNCTION IF EXISTS public.fcn_insert_pessoa(integer, character varying, character varying, character varying, character varying, integer, character varying, character varying, integer, integer) CASCADE;');
        DB::unprepared('DROP FUNCTION IF EXISTS public.fcn_obter_primeiro_ultimo_nome_juridica(text) CASCADE;');
        DB::unprepared('DROP FUNCTION IF EXISTS public.fcn_update_documento(integer, character varying, character varying, character varying, character varying, integer, integer, integer, integer, character varying, character varying, character varying, character varying, integer, integer, character varying, character varying, integer, integer, character, integer, integer) CASCADE;');
        DB::unprepared('DROP FUNCTION IF EXISTS public.fcn_update_endereco_externo(integer, integer, character varying, character varying, character varying, integer, character varying, character varying, character varying, integer, character varying, character varying, character, integer, integer) CASCADE;');
        DB::unprepared('DROP FUNCTION IF EXISTS public.fcn_update_endereco_pessoa(integer, integer, integer, integer, integer, integer, character varying, character varying, character varying, character, integer, integer) CASCADE;');
        DB::unprepared('DROP FUNCTION IF EXISTS public.fcn_update_fisica_cpf(integer, text, character, integer, integer) CASCADE;');
        DB::unprepared('DROP FUNCTION IF EXISTS public.fcn_update_fisica(integer, character varying, character varying, integer, integer, integer, integer, integer, integer, character varying, character varying, integer, integer, character varying, integer, character varying, integer, character varying, character varying, character varying, character varying, character varying, character, integer, integer) CASCADE;');
        DB::unprepared('DROP FUNCTION IF EXISTS public.fcn_update_fone_pessoa(integer, integer, integer, integer, character, integer, integer) CASCADE;');
        DB::unprepared('DROP FUNCTION IF EXISTS public.fcn_delete_endereco_pessoa(integer, integer) CASCADE;');
        DB::unprepared('DROP FUNCTION IF EXISTS public.fcn_delete_fone_pessoa(integer) CASCADE;');
        DB::unprepared('DROP FUNCTION IF EXISTS public.fcn_fonetiza_palavra(text) CASCADE;');
        DB::unprepared('DROP FUNCTION IF EXISTS public.fcn_fonetiza_pessoa_geral() CASCADE;');
        DB::unprepared('DROP FUNCTION IF EXISTS public.fcn_fonetiza_primeiro_ultimo_nome(text) CASCADE;');
        DB::unprepared('DROP FUNCTION IF EXISTS public.fcn_insert_documento(integer, character varying, character varying, character varying, character varying, integer, integer, integer, integer, character varying, character varying, character varying, character varying, integer, integer, character varying, character varying, integer, integer, character, integer, integer) CASCADE;');
        DB::unprepared('DROP FUNCTION IF EXISTS public.fcn_insert_endereco_externo(integer, integer, character varying, character varying, character varying, integer, character varying, character varying, character varying, integer, character varying, character varying, character, integer, integer) CASCADE;');
        DB::unprepared('DROP FUNCTION IF EXISTS public.fcn_insert_endereco_pessoa(integer, integer, integer, integer, integer, integer, character varying, character varying, character varying, character, integer, integer) CASCADE;');
        DB::unprepared('DROP FUNCTION IF EXISTS public.fcn_update_juridica(integer, character varying, character varying, character varying, character, integer, integer) CASCADE;');
        DB::unprepared('DROP FUNCTION IF EXISTS public.fcn_update_pessoa(integer, text, character varying, character varying, character varying, integer, character varying, integer, integer) CASCADE;');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->dropSchemas();
        $this->dropFromPmieducar();
        $this->dropFromPortal();
        $this->dropFromPublic();
    }
}
