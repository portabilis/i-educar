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
                
                DROP SEQUENCE portal_banner_cod_portal_banner_seq;
                
                DROP SEQUENCE regiao_cod_regiao_seq;
                
                DROP SEQUENCE setor_idset_seq;
         
                SET search_path = pmicontrolesis, pg_catalog;
                
                DROP SEQUENCE acontecimento_cod_acontecimento_seq;
                
                DROP SEQUENCE artigo_cod_artigo_seq;
                
                DROP SEQUENCE foto_evento_cod_foto_evento_seq;
                
                DROP SEQUENCE foto_vinc_cod_foto_vinc_seq;
                
                DROP SEQUENCE itinerario_cod_itinerario_seq;
                
                DROP SEQUENCE portais_cod_portais_seq;
                
                DROP SEQUENCE servicos_cod_servicos_seq;
                
                DROP SEQUENCE sistema_cod_sistema_seq;
                
                DROP SEQUENCE telefones_cod_telefones_seq;
                
                DROP SEQUENCE tipo_acontecimento_cod_tipo_acontecimento_seq;
                
                DROP SEQUENCE topo_portal_cod_topo_portal_seq;
                
                SET search_path = portal, pg_catalog;
                
                DROP SEQUENCE agenda_pref_cod_comp_seq;
                
                DROP SEQUENCE compras_editais_editais_cod_compras_editais_editais_seq;
                
                DROP SEQUENCE compras_editais_empresa_cod_compras_editais_empresa_seq;
                
                DROP SEQUENCE compras_final_pregao_cod_compras_final_pregao_seq;
                
                DROP SEQUENCE compras_licitacoes_cod_compras_licitacoes_seq;
                
                DROP SEQUENCE compras_modalidade_cod_compras_modalidade_seq;
                
                DROP SEQUENCE compras_pregao_execucao_cod_compras_pregao_execucao_seq;
                
                DROP SEQUENCE compras_prestacao_contas_cod_compras_prestacao_contas_seq;
                
                DROP SEQUENCE foto_portal_cod_foto_portal_seq;
                
                DROP SEQUENCE foto_secao_cod_foto_secao_seq;
                
                DROP SEQUENCE jor_edicao_cod_jor_edicao_seq;
                
                DROP SEQUENCE mailling_email_cod_mailling_email_seq;
                
                DROP SEQUENCE mailling_email_conteudo_cod_mailling_email_conteudo_seq;
                
                DROP SEQUENCE mailling_fila_envio_cod_mailling_fila_envio_seq;
                
                DROP SEQUENCE mailling_grupo_cod_mailling_grupo_seq;
                
                DROP SEQUENCE mailling_historico_cod_mailling_historico_seq;
                
                DROP SEQUENCE not_portal_cod_not_portal_seq;
                
                DROP SEQUENCE not_tipo_cod_not_tipo_seq;
                
                DROP SEQUENCE notificacao_cod_notificacao_seq;
                
                DROP SEQUENCE pessoa_atividade_cod_pessoa_atividade_seq;
                
                DROP SEQUENCE pessoa_fj_cod_pessoa_fj_seq;
                
                DROP SEQUENCE pessoa_ramo_atividade_cod_ramo_atividade_seq;
                
                DROP SEQUENCE portal_banner_cod_portal_banner_seq;
                
                DROP SEQUENCE portal_concurso_cod_portal_concurso_seq;
                
                DROP SEQUENCE sistema_cod_sistema_seq;
                
                SET search_path = public, pg_catalog;
            '
        );
    }
}
