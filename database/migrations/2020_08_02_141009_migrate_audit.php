<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class MigrateAudit extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            DB::select('select 1 from modules.auditoria_geral LIMIT 1');
        } catch (\Throwable $e) {
            return;
        }

        DB::unprepared(<<<'EOL'
                CREATE OR REPLACE function public.get_table(rotina varchar)
                    RETURNS TABLE (
                                      table_schema VARCHAR,
                                      table_name VARCHAR
                                  )
                as $$

                DECLARE
                    name varchar;

                BEGIN

                    name := CASE
                                WHEN rotina = 'tabela_arredondamento_valor' THEN 'modules.tabela_arredondamento_valor'
                                WHEN rotina = 'regra_avaliacao' THEN 'modules.regra_avaliacao'
                                WHEN rotina = 'servidor_funcao' THEN 'pmieducar.servidor_funcao'
                                WHEN rotina = 'TRIGGER_NOTA_COMPONENTE_CURRICULAR' THEN 'modules.nota_componente_curricular'
                                WHEN rotina = 'quadro_horario' THEN 'pmieducar.quadro_horario'
                                WHEN rotina = 'transporte_aluno' THEN 'modules.transporte_aluno'
                                WHEN rotina = 'componente_curricular_turma' THEN 'modules.componente_curricular_turma'
                                WHEN rotina = 'professor_turma_disciplina' THEN 'modules.professor_turma_disciplina'
                                WHEN rotina = 'componente_curricular_turma' THEN 'modules.componente_curricular_turma'
                                WHEN rotina = 'parecer_aluno' THEN 'modules.parecer_aluno'
                                WHEN rotina = 'funcionario' THEN 'portal.funcionario'
                                WHEN rotina = 'componente_curricular' THEN 'modules.componente_curricular'
                                WHEN rotina = 'funcionario' THEN 'portal.funcionario'
                                WHEN rotina = 'TRIGGER_MATRICULA' THEN 'pmieducar.matricula'
                                WHEN rotina = 'configuracoes_gerais' THEN 'pmieducar.configuracoes_gerais'
                                WHEN rotina = 'nota_componente_curricular_media' THEN 'modules.nota_componente_curricular_media'
                                WHEN rotina = 'escola' THEN 'pmieducar.escola'
                                WHEN rotina = 'historico_escolar' THEN 'pmieducar.historico_escolar'
                                WHEN rotina = 'servidor' THEN 'pmieducar.servidor'
                                WHEN rotina = 'TRIGGER_PARECER_GERAL' THEN 'modules.parecer_geral'
                                WHEN rotina = 'educacenso_cod_aluno' THEN 'modules.educacenso_cod_aluno'
                                WHEN rotina = 'instituicao' THEN 'pmieducar.instituicao'
                                WHEN rotina = 'componente_curricular_ano_escolar' THEN 'modules.componente_curricular_ano_escolar'
                                WHEN rotina = 'serie' THEN 'pmieducar.serie'
                                WHEN rotina = 'modulo' THEN 'pmieducar.modulo'
                                WHEN rotina = 'nota_componente_curricular' THEN 'modules.nota_componente_curricular'
                                WHEN rotina = 'escola_serie' THEN 'pmieducar.escola_serie'
                                WHEN rotina = 'update_registration_status' THEN 'pmieducar.matricula'
                                WHEN rotina = 'usuario' THEN 'pmieducar.usuario'
                                WHEN rotina = 'falta_componente_curricular' THEN 'modules.falta_componente_curricular'
                                WHEN rotina = 'TRIGGER_FALTA_GERAL' THEN 'modules.falta_geral'
                                WHEN rotina = 'aluno' THEN 'pmieducar.aluno'
                                WHEN rotina = 'transferencia_tipo' THEN 'pmieducar.transferencia_tipo'
                                WHEN rotina = 'parecer_geral' THEN 'modules.parecer_geral'
                                WHEN rotina = 'professor_turma' THEN 'modules.professor_turma'
                                WHEN rotina = 'TRIGGER_NOTA_COMPONENTE_CURRICULAR_MEDIA' THEN 'modules.nota_componente_curricular_media'
                                WHEN rotina = 'tabela_arredondamento' THEN 'modules.tabela_arredondamento'
                                WHEN rotina = 'area_conhecimento' THEN 'modules.area_conhecimento'
                                WHEN rotina = 'formula_media' THEN 'modules.formula_media'
                                WHEN rotina = 'fisica' THEN 'cadastro.fisica'
                                WHEN rotina = 'aluno_beneficio' THEN 'pmieducar.aluno_beneficio'
                                WHEN rotina = 'motivo_afastamento' THEN 'pmieducar.motivo_afastamento'
                                WHEN rotina = 'TRIGGER_NOTA_EXAME' THEN 'modules.nota_exame'
                                WHEN rotina = 'falta_aluno' THEN 'modules.falta_aluno'
                                WHEN rotina = 'TRIGGER_FALTA_COMPONENTE_CURRICULAR' THEN 'modules.falta_componente_curricular'
                                WHEN rotina = 'juridica' THEN 'cadastro.juridica'
                                WHEN rotina = 'TRIGGER_MATRICULA_TURMA' THEN 'pmieducar.matricula_turma'
                                WHEN rotina = 'matricula_turma' THEN 'pmieducar.matricula_turma'
                                WHEN rotina = 'educacenso_cod_escola' THEN 'modules.educacenso_cod_escola'
                                WHEN rotina = 'falta_geral' THEN 'modules.falta_geral'
                                WHEN rotina = 'regra_avaliacao_serie_ano' THEN 'modules.regra_avaliacao_serie_ano'
                                WHEN rotina = 'tipo_usuario' THEN 'pmieducar.tipo_usuario'
                                WHEN rotina = 'matricula' THEN 'pmieducar.matricula'
                                WHEN rotina = 'curso' THEN 'pmieducar.curso'
                                WHEN rotina = 'nota_aluno' THEN 'modules.nota_aluno'
                                WHEN rotina = 'pessoa' THEN 'cadastro.pessoa'
                                WHEN rotina = 'turma' THEN 'pmieducar.turma'
                                WHEN rotina = 'tipo_ocorrencia_disciplinar' THEN 'pmieducar.tipo_ocorrencia_disciplinar'
                                WHEN rotina = 'calendario_dia_motivo' THEN 'pmieducar.calendario_dia_motivo'
                                WHEN rotina = 'tipo_dispensa' THEN 'pmieducar.tipo_dispensa'
                                WHEN rotina = 'tipo_ensino' THEN 'pmieducar.tipo_ensino'
                                WHEN rotina = 'habilitacao' THEN 'pmieducar.habilitacao'
                                WHEN rotina = 'escola_localizacao' THEN 'pmieducar.escola_localizacao'
                                WHEN rotina = 'escola_rede_ensino' THEN 'pmieducar.escola_rede_ensino'
                                WHEN rotina = 'tipo_regime' THEN 'pmieducar.tipo_regime'
                                WHEN rotina = 'nivel_ensino' THEN 'pmieducar.nivel_ensino'
                                WHEN rotina = 'turma_tipo' THEN 'pmieducar.turma_tipo'
                                WHEN rotina = 'infra_predio' THEN 'pmieducar.turma_tipo'
                                WHEN rotina = 'matricula_ocorrencia_disciplinar' THEN 'pmieducar.matricula_ocorrencia_disciplinar'
                                WHEN rotina = 'escolaridade' THEN 'cadastro.escolaridade'
                                WHEN rotina = 'categoria_nivel' THEN 'pmieducar.categoria_nivel'
                                WHEN rotina = 'empresa_transporte_escolar' THEN 'modules.empresa_transporte_escolar'
                                WHEN rotina = 'motorista' THEN 'modules.motorista'
                                WHEN rotina = 'rota_transporte_escolar' THEN 'modules.rota_transporte_escolar'
                                WHEN rotina = 'pessoa_transporte' THEN 'modules.pessoa_transporte'
                                WHEN rotina = 'categoria_obra' THEN 'pmieducar.categoria_obra'
                                WHEN rotina = 'acervo_assunto' THEN 'pmieducar.acervo_assunto'
                                WHEN rotina = 'acervo_autor' THEN 'pmieducar.acervo_autor'
                                WHEN rotina = 'acervo_colecao' THEN 'pmieducar.acervo_colecao'
                                WHEN rotina = 'acervo_editora' THEN 'pmieducar.acervo_editora'
                                WHEN rotina = 'acervo_idioma' THEN 'pmieducar.acervo_idioma'
                                WHEN rotina = 'fonte' THEN 'pmieducar.fonte'
                                WHEN rotina = 'exemplar_tipo' THEN 'pmieducar.exemplar_tipo'
                                WHEN rotina = 'situacao' THEN 'pmieducar.situacao'
                                WHEN rotina = 'biblioteca' THEN 'pmieducar.biblioteca'
                                WHEN rotina = 'infra_comodo_funcao' THEN 'pmieducar.infra_comodo_funcao'
                                WHEN rotina = 'dispensa_disciplina' THEN 'pmieducar.dispensa_disciplina'
                                WHEN rotina = 'ponto_transporte_escolar' THEN 'pmieducar.ponto_transporte_escolar'
                                WHEN rotina = 'candidato_reserva_vaga' THEN 'pmieducar.candidato_reserva_vaga'
                                WHEN rotina = 'candidato_fila_unica' THEN 'pmieducar.candidato_fila_unica'
                                WHEN rotina = 'regra_avaliacao_recuperacao' THEN 'modules.regra_avaliacao_recuperacao'
                                WHEN rotina = 'parecer_componente_curricular' THEN 'modules.parecer_componente_curricular'
                                WHEN rotina = 'TRIGGER_PARECER_COMPONENTE_CURRICULAR' THEN 'modules.parecer_componente_curricular'
                                WHEN rotina = 'calendario_ano_letivo' THEN 'pmieducar.calendario_ano_letivo'
                                WHEN rotina = 'Endereçamento de Municipio' THEN 'public.municipio'
                                WHEN rotina = 'Endereçamento de Bairro' THEN 'public.bairro'
                                WHEN rotina = 'Endereçamento de CEP' THEN 'urbano.cep_logradouro'
                                WHEN rotina = 'Endereçamento de Estado' THEN 'public.uf'
                                WHEN rotina = 'infra_predio_comodo' THEN 'pmieducar.infra_predio_comodo'
                                WHEN rotina = 'calendario_anotacao' THEN 'pmieducar.calendario_anotacao'
                                WHEN rotina = 'deficiencia' THEN 'cadastro.deficiencia'
                                WHEN rotina = 'turma_modulo' THEN 'pmieducar.turma_modulo'
                                WHEN rotina = 'abandono_tipo' THEN 'pmieducar.abandono_tipo'
                                WHEN rotina = 'config_movimento_geral' THEN 'modules.config_movimento_geral'
                                WHEN rotina = 'Endereçamento de Logradouro' THEN 'public.logradouro'
                                WHEN rotina = 'acervo' THEN 'pmieducar.acervo'
                                WHEN rotina = 'calendario_dia' THEN 'pmieducar.calendario_dia'
                                WHEN rotina = 'calendario_turma' THEN 'pmieducar.calendario_turma'
                                WHEN rotina = 'cliente' THEN 'pmieducar.cliente'
                                WHEN rotina = 'cliente_tipo' THEN 'pmieducar.cliente_tipo'
                                WHEN rotina = 'motivo_suspensao' THEN 'pmieducar.motivo_suspensao'
                                WHEN rotina = 'exemplar' THEN 'pmieducar.exemplar'
                                WHEN rotina = 'reservas' THEN 'pmieducar.reservas'
                                WHEN rotina = 'veiculo' THEN 'modules.veiculo'
                                WHEN rotina = 'itinerario_transporte_escolar' THEN 'modules.itinerario_transporte_escolar'
                                WHEN rotina = 'TRIGGER_NOTA_GERAL' THEN 'modules.nota_geral'
                                ELSE 'legacy.legacy'
                        END;

                    IF name = '' THEN
                        RAISE EXCEPTION 'Rotina % não encontrada', rotina;
                    END IF;

                    RETURN query SELECT tbl[1]::varchar, tbl[2]::varchar FROM string_to_array(name, '.') tbl;
                END;
                $$
                LANGUAGE 'plpgsql';

                INSERT INTO public.ieducar_audit (context, before, after, schema, "table", date)
                SELECT
                    cast (E'{"user_id":'|| usuario_id ||',"user_name":"' || COALESCE(pessoa.nome, 'legacy') || '","origin":"legacy"}' AS json),
                    auditoria_geral.valor_antigo,
                    auditoria_geral.valor_novo,
                    (SELECT table_schema FROM public.get_table(auditoria_geral.rotina)),
                    (SELECT table_name FROM public.get_table(auditoria_geral.rotina)),
                    auditoria_geral.data_hora
                FROM modules.auditoria_geral
                LEFT JOIN cadastro.pessoa ON pessoa.idpes = auditoria_geral.usuario_id;
EOL);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
