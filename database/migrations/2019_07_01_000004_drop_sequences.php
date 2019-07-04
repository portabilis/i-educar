<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class DropSequences extends Migration
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
                
                DROP SEQUENCE IF EXISTS portal_banner_cod_portal_banner_seq;
                
                DROP SEQUENCE IF EXISTS regiao_cod_regiao_seq;
                
                DROP SEQUENCE IF EXISTS setor_idset_seq;
         
                SET search_path = portal, pg_catalog;
                                
                DROP SEQUENCE IF EXISTS agenda_pref_cod_comp_seq;
                
                DROP SEQUENCE IF EXISTS compras_editais_editais_cod_compras_editais_editais_seq;
                
                DROP SEQUENCE IF EXISTS compras_editais_empresa_cod_compras_editais_empresa_seq;
                
                DROP SEQUENCE IF EXISTS compras_final_pregao_cod_compras_final_pregao_seq;
                
                DROP SEQUENCE IF EXISTS compras_licitacoes_cod_compras_licitacoes_seq;
                
                DROP SEQUENCE IF EXISTS compras_modalidade_cod_compras_modalidade_seq;
                
                DROP SEQUENCE IF EXISTS compras_pregao_execucao_cod_compras_pregao_execucao_seq;
                
                DROP SEQUENCE IF EXISTS compras_prestacao_contas_cod_compras_prestacao_contas_seq;
                
                DROP SEQUENCE IF EXISTS foto_portal_cod_foto_portal_seq;
                
                DROP SEQUENCE IF EXISTS foto_secao_cod_foto_secao_seq;
                
                DROP SEQUENCE IF EXISTS imagem_cod_imagem_seq;
                
                DROP SEQUENCE IF EXISTS imagem_tipo_cod_imagem_tipo_seq;
                
                DROP SEQUENCE IF EXISTS intranet_segur_permissao_nega_cod_intranet_segur_permissao__seq;
                
                DROP SEQUENCE IF EXISTS jor_edicao_cod_jor_edicao_seq;
                
                DROP SEQUENCE IF EXISTS mailling_email_cod_mailling_email_seq;
                
                DROP SEQUENCE IF EXISTS mailling_email_conteudo_cod_mailling_email_conteudo_seq;
                
                DROP SEQUENCE IF EXISTS mailling_fila_envio_cod_mailling_fila_envio_seq;
                
                DROP SEQUENCE IF EXISTS mailling_grupo_cod_mailling_grupo_seq;
                
                DROP SEQUENCE IF EXISTS mailling_historico_cod_mailling_historico_seq;
                
                DROP SEQUENCE IF EXISTS menu_menu_cod_menu_menu_seq;
                
                DROP SEQUENCE IF EXISTS menu_submenu_cod_menu_submenu_seq;

                DROP SEQUENCE IF EXISTS not_portal_cod_not_portal_seq;
                
                DROP SEQUENCE IF EXISTS not_tipo_cod_not_tipo_seq;
                
                DROP SEQUENCE IF EXISTS notificacao_cod_notificacao_seq;
                
                DROP SEQUENCE IF EXISTS pessoa_atividade_cod_pessoa_atividade_seq;
                
                DROP SEQUENCE IF EXISTS pessoa_fj_cod_pessoa_fj_seq;
                
                DROP SEQUENCE IF EXISTS pessoa_ramo_atividade_cod_ramo_atividade_seq;
                
                DROP SEQUENCE IF EXISTS portal_banner_cod_portal_banner_seq;
                
                DROP SEQUENCE IF EXISTS portal_concurso_cod_portal_concurso_seq;
                
                DROP SEQUENCE IF EXISTS sistema_cod_sistema_seq;
                
                SET search_path = public, pg_catalog;
            '
        );
    }
}
