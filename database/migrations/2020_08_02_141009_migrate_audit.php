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
                                ELSE ''
                        END;

                    IF name = '' THEN
                        RAISE EXCEPTION 'Rotina % não encontrada', rotina;
                    END IF;

                    RETURN query SELECT tbl[1]::varchar, tbl[2]::varchar FROM string_to_array(name, '.') tbl;
                END;
                $$
                LANGUAGE 'plpgsql';

                INSERT INTO public.audit (context, before, after, schema, "table", date)
                SELECT
                    cast ('{"user_id":'|| usuario_id ||',"user_name":"' || pessoa.nome || '","origin":""}' AS json),
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
