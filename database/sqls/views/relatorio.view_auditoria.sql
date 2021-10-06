SET search_path = relatorio, pmieducar, public, pg_catalog;


CREATE OR REPLACE VIEW relatorio.view_auditoria AS
SELECT (substr((auditoria.usuario)::text, 0, strpos((auditoria.usuario)::text, '-'::text)))::integer AS usuario_id,
       substr((auditoria.usuario)::text, (strpos((auditoria.usuario)::text, '-'::text) + 2)) AS usuario_matricula,
       auditoria.operacao,
       auditoria.rotina,
       auditoria.data_hora,
       CASE
           WHEN (auditoria.operacao = 1) THEN get_valor_campo_auditoria('instituicao'::character varying, 'instituicao_id'::character varying, (auditoria.valor_novo)::character varying)
           ELSE get_valor_campo_auditoria('instituicao'::character varying, 'instituicao_id'::character varying, (auditoria.valor_antigo)::character varying)
           END AS instituicao,
       (
           CASE
               WHEN (auditoria.operacao = 1) THEN get_valor_campo_auditoria('instituicao_id'::character varying, 'escola'::character varying, (auditoria.valor_novo)::character varying)
               ELSE get_valor_campo_auditoria('instituicao_id'::character varying, 'escola'::character varying, (auditoria.valor_antigo)::character varying)
               END)::integer AS instituicao_id,
       CASE
           WHEN (auditoria.operacao = 1) THEN get_valor_campo_auditoria('escola'::character varying, 'escola_id'::character varying, (auditoria.valor_novo)::character varying)
           ELSE get_valor_campo_auditoria('escola'::character varying, 'escola_id'::character varying, (auditoria.valor_antigo)::character varying)
           END AS escola,
       (
           CASE
               WHEN (auditoria.operacao = 1) THEN get_valor_campo_auditoria('escola_id'::character varying, 'curso'::character varying, (auditoria.valor_novo)::character varying)
               ELSE get_valor_campo_auditoria('escola_id'::character varying, 'curso'::character varying, (auditoria.valor_antigo)::character varying)
               END)::integer AS escola_id,
       CASE
           WHEN (auditoria.operacao = 1) THEN get_valor_campo_auditoria('curso'::character varying, 'curso_id'::character varying, (auditoria.valor_novo)::character varying)
           ELSE get_valor_campo_auditoria('curso'::character varying, 'curso_id'::character varying, (auditoria.valor_antigo)::character varying)
           END AS curso,
       (
           CASE
               WHEN (auditoria.operacao = 1) THEN get_valor_campo_auditoria('curso_id'::character varying, 'serie'::character varying, (auditoria.valor_novo)::character varying)
               ELSE get_valor_campo_auditoria('curso_id'::character varying, 'serie'::character varying, (auditoria.valor_antigo)::character varying)
               END)::integer AS curso_id,
       CASE
           WHEN (auditoria.operacao = 1) THEN get_valor_campo_auditoria('serie'::character varying, 'serie_id'::character varying, (auditoria.valor_novo)::character varying)
           ELSE get_valor_campo_auditoria('serie'::character varying, 'serie_id'::character varying, (auditoria.valor_antigo)::character varying)
           END AS serie,
       (
           CASE
               WHEN (auditoria.operacao = 1) THEN get_valor_campo_auditoria('serie_id'::character varying, 'turma'::character varying, (auditoria.valor_novo)::character varying)
               ELSE get_valor_campo_auditoria('serie_id'::character varying, 'turma'::character varying, (auditoria.valor_antigo)::character varying)
               END)::integer AS serie_id,
       CASE
           WHEN (auditoria.operacao = 1) THEN get_valor_campo_auditoria('turma'::character varying, 'turma_id'::character varying, (auditoria.valor_novo)::character varying)
           ELSE get_valor_campo_auditoria('turma'::character varying, 'turma_id'::character varying, (auditoria.valor_antigo)::character varying)
           END AS turma,
       (
           CASE
               WHEN (auditoria.operacao = 1) THEN get_valor_campo_auditoria('turma_id'::character varying, 'aluno'::character varying, (auditoria.valor_novo)::character varying)
               ELSE get_valor_campo_auditoria('turma_id'::character varying, 'aluno'::character varying, (auditoria.valor_antigo)::character varying)
               END)::integer AS turma_id,
       CASE
           WHEN (auditoria.operacao = 1) THEN get_valor_campo_auditoria('aluno'::character varying, 'aluno_id'::character varying, (auditoria.valor_novo)::character varying)
           ELSE get_valor_campo_auditoria('aluno'::character varying, 'aluno_id'::character varying, (auditoria.valor_antigo)::character varying)
           END AS aluno,
       (
           CASE
               WHEN (auditoria.operacao = 1) THEN get_valor_campo_auditoria('aluno_id'::character varying, 'nota'::character varying, (auditoria.valor_novo)::character varying)
               ELSE get_valor_campo_auditoria('aluno_id'::character varying, 'nota'::character varying, (auditoria.valor_antigo)::character varying)
               END)::integer AS aluno_id,
       CASE
           WHEN (auditoria.operacao = 1) THEN get_valor_campo_auditoria('etapa'::character varying, 'componenteCurricular'::character varying, (auditoria.valor_novo)::character varying)
           ELSE get_valor_campo_auditoria('etapa'::character varying, 'componenteCurricular'::character varying, (auditoria.valor_antigo)::character varying)
           END AS etapa,
       CASE
           WHEN (auditoria.operacao = 1) THEN get_valor_campo_auditoria('componenteCurricular'::character varying, ''::character varying, (auditoria.valor_novo)::character varying)
           ELSE get_valor_campo_auditoria('componenteCurricular'::character varying, ''::character varying, (auditoria.valor_antigo)::character varying)
           END AS componente_curricular,
       get_valor_campo_auditoria('nota'::character varying, 'etapa'::character varying, (auditoria.valor_antigo)::character varying) AS nota_antiga,
       get_valor_campo_auditoria('nota'::character varying, 'etapa'::character varying, (auditoria.valor_novo)::character varying) AS nota_nova
FROM modules.auditoria;


SET search_path = public, pg_catalog, relatorio, pmieducar;
