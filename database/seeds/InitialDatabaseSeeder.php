<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InitialDatabaseSeeder extends Seeder
{
    /**
     * Return all files for seed.
     *
     * @return array
     */
    private function getFiles()
    {
        return [
            '00-config.sql',
            '01-schemas.sql',
            '02-extensions.sql',
            '03-types.sql',
            '04-functions.sql',
            '05-aggregates.sql',
            '06-sequences.sql',
            '07-tables.sql',
            '08-add-sequences.sql',
            '09-views.sql',
            '10-acesso.sistema.sql',
            '10-cadastro.codigo_cartorio_inep.sql',
            '10-cadastro.deficiencia.sql',
            '10-cadastro.escolaridade.sql',
            '10-cadastro.estado_civil.sql',
            '10-cadastro.fisica.sql',
            '10-cadastro.orgao_emissor_rg.sql',
            '10-cadastro.pessoa.sql',
            '10-cadastro.raca.sql',
            '10-consistenciacao.campo_consistenciacao.sql',
            '10-historico.municipio.sql',
            '10-modules.educacenso_curso_superior.sql',
            '10-modules.educacenso_ies.sql',
            '10-modules.educacenso_orgao_regional.sql',
            '10-modules.etapas_educacenso.sql',
            '10-modules.formula_media.sql',
            '10-modules.lingua_indigena_educacenso.sql',
            '10-modules.regra_avaliacao.sql',
            '10-modules.tabela_arredondamento.sql',
            '10-modules.tabela_arredondamento_valor.sql',
            '10-modules.tipo_veiculo.sql',
            '10-pmicontrolesis.menu.sql',
            '10-pmicontrolesis.tutormenu.sql',
            '10-pmieducar.abandono_tipo.sql',
            '10-pmieducar.backup.sql',
            '10-pmieducar.configuracoes_gerais.sql',
            '10-pmieducar.escola_localizacao.sql',
            '10-pmieducar.historico_educar.sql',
            '10-pmieducar.historico_grade_curso.sql',
            '10-pmieducar.instituicao.sql',
            '10-pmieducar.menu_tipo_usuario.sql',
            '10-pmieducar.tipo_autor.sql',
            '10-pmieducar.tipo_usuario.sql',
            '10-pmieducar.turma_turno.sql',
            '10-pmieducar.usuario.sql',
            '10-portal.acesso.sql',
            '10-portal.agenda.sql',
            '10-portal.funcionario.sql',
            '10-portal.funcionario_vinculo.sql',
            '10-portal.imagem.sql',
            '10-portal.imagem_tipo.sql',
            '10-portal.menu_funcionario.sql',
            '10-portal.menu_menu.sql',
            '10-portal.menu_submenu.sql',
            '10-public.changelog.sql',
            '10-public.distrito.sql',
            '10-public.municipio.sql',
            '10-public.pais.sql',
            '10-public.phinxlog.sql',
            '10-public.uf.sql',
            '10-urbano.tipo_logradouro.sql',
            '11-next-sequences.sql',
            '12-indexes.sql',
            '13-triggers.sql',
            '14-foreign-keys.sql',
        ];
    }

    /**
     * Return all functions files for seed.
     *
     * @return array
     */
    private function getFunctionsFiles()
    {
        return [
            'alimentos.fcn_calcular_qtde_cardapio.sql',
            'alimentos.fcn_calcular_qtde_percapita.sql',
            'alimentos.fcn_calcular_qtde_unidade.sql',
            'alimentos.fcn_gerar_guia_remessa.sql',
            'cadastro.fcn_aft_documento.sql',
            'cadastro.fcn_aft_fisica.sql',
            'cadastro.fcn_aft_fisica_cpf_provisorio.sql',
            'cadastro.fcn_aft_fisica_provisorio.sql',
            'cadastro.fcn_aft_ins_endereco_externo.sql',
            'cadastro.fcn_aft_ins_endereco_pessoa.sql',
            'consistenciacao.fcn_delete_temp_cadastro_unificacao_cmf.sql',
            'consistenciacao.fcn_delete_temp_cadastro_unificacao_siam.sql',
            'consistenciacao.fcn_documento_historico_campo.sql',
            'consistenciacao.fcn_endereco_externo_historico_campo.sql',
            'consistenciacao.fcn_endereco_pessoa_historico_campo.sql',
            'consistenciacao.fcn_fisica_historico_campo.sql',
            'consistenciacao.fcn_fone_historico_campo.sql',
            'consistenciacao.fcn_gravar_historico_campo.sql',
            'consistenciacao.fcn_juridica_historico_campo.sql',
            'consistenciacao.fcn_pessoa_historico_campo.sql',
            'consistenciacao.fcn_unifica_cadastro.sql',
            'consistenciacao.fcn_unifica_cmf.sql',
            'consistenciacao.fcn_unifica_sca.sql',
            'consistenciacao.fcn_unifica_scd.sql',
            'consistenciacao.fcn_unifica_sgp.sql',
            'consistenciacao.fcn_unifica_sgpa.sql',
            'consistenciacao.fcn_unifica_sgsp.sql',
            'conv_functions.pr_normaliza_enderecos.sql',
            'historico.fcn_delete_grava_historico_bairro.sql',
            'historico.fcn_delete_grava_historico_cep_logradouro.sql',
            'historico.fcn_delete_grava_historico_cep_logradouro_bairro.sql',
            'historico.fcn_delete_grava_historico_documento.sql',
            'historico.fcn_delete_grava_historico_endereco_externo.sql',
            'historico.fcn_delete_grava_historico_endereco_pessoa.sql',
            'historico.fcn_delete_grava_historico_fisica.sql',
            'historico.fcn_delete_grava_historico_fisica_cpf.sql',
            'historico.fcn_delete_grava_historico_fone_pessoa.sql',
            'historico.fcn_delete_grava_historico_funcionario.sql',
            'historico.fcn_delete_grava_historico_juridica.sql',
            'historico.fcn_delete_grava_historico_logradouro.sql',
            'historico.fcn_delete_grava_historico_municipio.sql',
            'historico.fcn_delete_grava_historico_pessoa.sql',
            'historico.fcn_delete_grava_historico_socio.sql',
            'historico.fcn_grava_historico_bairro.sql',
            'historico.fcn_grava_historico_cep_logradouro.sql',
            'historico.fcn_grava_historico_cep_logradouro_bairro.sql',
            'historico.fcn_grava_historico_documento.sql',
            'historico.fcn_grava_historico_endereco_externo.sql',
            'historico.fcn_grava_historico_endereco_pessoa.sql',
            'historico.fcn_grava_historico_fisica.sql',
            'historico.fcn_grava_historico_fisica_cpf.sql',
            'historico.fcn_grava_historico_fone_pessoa.sql',
            'historico.fcn_grava_historico_funcionario.sql',
            'historico.fcn_grava_historico_juridica.sql',
            'historico.fcn_grava_historico_logradouro.sql',
            'historico.fcn_grava_historico_municipio.sql',
            'historico.fcn_grava_historico_pessoa.sql',
            'historico.fcn_grava_historico_socio.sql',
            'modules.audita_falta_componente_curricular.sql',
            'modules.audita_falta_geral.sql',
            'modules.audita_media_geral.sql',
            'modules.audita_nota_componente_curricular.sql',
            'modules.audita_nota_componente_curricular_media.sql',
            'modules.audita_nota_exame.sql',
            'modules.audita_nota_geral.sql',
            'modules.audita_parecer_componente_curricular.sql',
            'modules.audita_parecer_geral.sql',
            'modules.copia_notas_transf.sql',
            'modules.corrige_sequencial_historico.sql',
            'modules.frequencia_da_matricula.sql',
            'modules.frequencia_etapa_padrao_ano_escolar_um.sql',
            'modules.frequencia_etapa_padrao_ano_escolar_zero.sql',
            'modules.frequencia_matricula_por_etapa.sql',
            'modules.frequencia_por_componente.sql',
            'modules.impede_duplicacao_falta_aluno.sql',
            'modules.impede_duplicacao_nota_aluno.sql',
            'modules.impede_duplicacao_parecer_aluno.sql',
            'modules.preve_data_emprestimo.sql',
            'pmieducar.audita_matricula.sql',
            'pmieducar.audita_matricula_turma.sql',
            'pmieducar.copiaanosletivos.sql',
            'pmieducar.fcn_aft_update.sql',
            'pmieducar.migra_beneficios_para_tabela_aluno_aluno_beneficio.sql',
            'pmieducar.normalizadeficienciaservidor.sql',
            'pmieducar.unifica_alunos.sql',
            'pmieducar.unifica_pessoas.sql',
            'pmieducar.unifica_tipos_transferencia.sql',
            'pmieducar.updated_at_matricula.sql',
            'pmieducar.updated_at_matricula_turma.sql',
            'public.commacat_ignore_nulls.sql',
            'public.count_weekdays.sql',
            'public.cria_distritos.sql',
            'public.data_para_extenso.sql',
            'public.f_unaccent.sql',
            'public.fcn_aft_logradouro_fonetiza.sql',
            'public.fcn_aft_pessoa_fonetiza.sql',
            'public.fcn_bef_ins_fisica.sql',
            'public.fcn_bef_ins_juridica.sql',
            'public.fcn_bef_logradouro_fonetiza.sql',
            'public.fcn_bef_pessoa_fonetiza.sql',
            'public.fcn_compara_nome_pessoa_fonetica.sql',
            'public.fcn_cons_log_fonetica.sql',
            'public.fcn_consulta_fonetica.sql',
            'public.fcn_delete_endereco_externo.sql',
            'public.fcn_delete_endereco_pessoa.sql',
            'public.fcn_delete_fone_pessoa.sql',
            'public.fcn_delete_funcionario.sql',
            'public.fcn_dia_util.sql',
            'public.fcn_fonetiza.sql',
            'public.fcn_fonetiza_logr_geral.sql',
            'public.fcn_fonetiza_palavra.sql',
            'public.fcn_fonetiza_pessoa_geral.sql',
            'public.fcn_fonetiza_primeiro_ultimo_nome.sql',
            'public.fcn_insert_documento.sql',
            'public.fcn_insert_endereco_externo.sql',
            'public.fcn_insert_endereco_pessoa.sql',
            'public.fcn_insert_fisica.sql',
            'public.fcn_insert_fisica_cpf.sql',
            'public.fcn_insert_fone_pessoa.sql',
            'public.fcn_insert_funcionario.sql',
            'public.fcn_insert_juridica.sql',
            'public.fcn_insert_pessoa.sql',
            'public.fcn_obter_primeiro_ultimo_nome.sql',
            'public.fcn_obter_primeiro_ultimo_nome_juridica.sql',
            'public.fcn_update_documento.sql',
            'public.fcn_update_endereco_externo.sql',
            'public.fcn_update_endereco_pessoa.sql',
            'public.fcn_update_fisica.sql',
            'public.fcn_update_fisica_cpf.sql',
            'public.fcn_update_fone_pessoa.sql',
            'public.fcn_update_funcionario.sql',
            'public.fcn_update_juridica.sql',
            'public.fcn_update_pessoa.sql',
            'public.fcn_upper.sql',
            'public.fcn_upper_nrm.sql',
            'public.formata_cpf.sql',
            'public.isnumeric.sql',
            'public.retira_data_cancel_matricula_fun.sql',
            'public.unifica_bairro.sql',
            'public.unifica_logradouro.sql',
            'public.update_updated_at.sql',
            'public.verifica_existe_matricula_posterior_mesma_turma.sql',
            'relatorio.count_weekdays.sql',
            'relatorio.formata_nome.sql',
            'relatorio.get_nome_escola.sql',
            'relatorio.get_texto_sem_caracter_especial.sql',
            'relatorio.get_texto_sem_espaco.sql',
        ];
    }

    /**
     * Return all views files for seed.
     *
     * @return array
     */
    private function getViewsFiles()
    {
        return [
            'cadastro.v_endereco.sql',
            'cadastro.v_fone_pessoa.sql',
            'cadastro.v_pessoa_fisica.sql',
            'cadastro.v_pessoa_fisica_simples.sql',
            'cadastro.v_pessoa_fj.sql',
            'cadastro.v_pessoa_juridica.sql',
            'cadastro.v_pessoafj_count.sql',
            'pmieducar.v_matricula_matricula_turma.sql',
            'portal.v_funcionario.sql',
            'relatorio.view_componente_curricular.sql',
        ];
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->getFiles() as $file) {

            if ($file === '04-functions.sql') {
                foreach ($this->getFunctionsFiles() as $functionFile) {
                    DB::unprepared(
                        file_get_contents(__DIR__ . '/../sqls/functions/' . $functionFile)
                    );
                }

                continue;
            }

            if ($file === '09-views.sql') {
                foreach ($this->getViewsFiles() as $viewFile) {
                    DB::unprepared(
                        file_get_contents(__DIR__ . '/../sqls/views/' . $viewFile)
                    );
                }

                continue;
            }

            DB::unprepared(
                file_get_contents(__DIR__ . '/../sqls/' . $file)
            );
        }

        DB::unprepared(
            '
                ALTER DATABASE ' . env('DB_DATABASE') . ' 
                SET search_path = "$user", public, portal, cadastro, acesso, alimentos, consistenciacao,
                historico, pmiacoes, pmicontrolesis, pmidrh, pmieducar, pmiotopic, urbano, modules;
            '
        );
    }
}
