--
-- @author   Caroline Salib Canto <caroline@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

-- Função de auditoria

 CREATE OR REPLACE FUNCTION relatorio.get_valor_campo_auditoria(varchar, varchar, varchar)
  RETURNS character varying AS
$BODY$SELECT CASE WHEN $2 = '' THEN substr($3, strpos($3, $1||':')+char_length($1)+1, ((strpos($3, '}')) - (strpos($3, $1)+char_length($1)+1)))
                  ELSE substr($3, strpos($3, $1||':')+char_length($1)+1, ((strpos($3, $2)-1) - (strpos($3, $1)+char_length($1)+1))) END as nome_instituicao;$BODY$
  LANGUAGE sql VOLATILE;
ALTER FUNCTION relatorio.get_valor_campo_auditoria(varchar, varchar, varchar)
  OWNER TO ieducar;

-- View de auditoria

  CREATE OR REPLACE VIEW relatorio.view_auditoria AS
select substr(auditoria.usuario, 0, strpos(auditoria.usuario, '-'))::int as usuario_id,
       substr(auditoria.usuario, strpos(auditoria.usuario, '-') + 2) as usuario_matricula,
       auditoria.operacao as operacao,
       auditoria.rotina as rotina,
       auditoria.data_hora as data_hora,
       (case when operacao = 1 then relatorio.get_valor_campo_auditoria('instituicao', 'instituicao_id', valor_novo) 
       else relatorio.get_valor_campo_auditoria('instituicao', 'instituicao_id', valor_antigo) end) as instituicao,
       (case when operacao = 1 then relatorio.get_valor_campo_auditoria('instituicao_id', 'escola', valor_novo) 
       else relatorio.get_valor_campo_auditoria('instituicao_id', 'escola', valor_antigo) end)::int as instituicao_id,
       (case when operacao = 1 then relatorio.get_valor_campo_auditoria('escola', 'escola_id', valor_novo) 
       else relatorio.get_valor_campo_auditoria('escola', 'escola_id', valor_antigo) end) as escola,
       (case when operacao = 1 then relatorio.get_valor_campo_auditoria('escola_id', 'curso', valor_novo)
       else relatorio.get_valor_campo_auditoria('escola_id', 'curso', valor_antigo) end)::int as escola_id,
       (case when operacao = 1 then relatorio.get_valor_campo_auditoria('curso', 'curso_id', valor_novo)
       else relatorio.get_valor_campo_auditoria('curso', 'curso_id', valor_antigo) end) as curso,
       (case when operacao = 1 then relatorio.get_valor_campo_auditoria('curso_id', 'serie', valor_novo)
       else relatorio.get_valor_campo_auditoria('curso_id', 'serie', valor_antigo) end)::int as curso_id,
       (case when operacao = 1 then relatorio.get_valor_campo_auditoria('serie', 'serie_id', valor_novo)
       else relatorio.get_valor_campo_auditoria('serie', 'serie_id', valor_antigo) end) as serie,
       (case when operacao = 1 then relatorio.get_valor_campo_auditoria('serie_id', 'turma', valor_novo)
       else relatorio.get_valor_campo_auditoria('serie_id', 'turma', valor_antigo) end)::int as serie_id,
       (case when operacao = 1 then relatorio.get_valor_campo_auditoria('turma', 'turma_id', valor_novo)
       else relatorio.get_valor_campo_auditoria('turma', 'turma_id', valor_antigo) end) as turma,
       (case when operacao = 1 then relatorio.get_valor_campo_auditoria('turma_id', 'aluno', valor_novo)
       else relatorio.get_valor_campo_auditoria('turma_id', 'aluno', valor_antigo) end)::int as turma_id,
       (case when operacao = 1 then relatorio.get_valor_campo_auditoria('aluno', 'aluno_id', valor_novo)
       else relatorio.get_valor_campo_auditoria('aluno', 'aluno_id', valor_antigo) end) as aluno,
       (case when operacao = 1 then relatorio.get_valor_campo_auditoria('aluno_id', 'nota', valor_novo)
       else relatorio.get_valor_campo_auditoria('aluno_id', 'nota', valor_antigo) end)::int as aluno_id,
       (case when operacao = 1 then relatorio.get_valor_campo_auditoria('etapa', 'componenteCurricular', valor_novo)
       else relatorio.get_valor_campo_auditoria('etapa', 'componenteCurricular', valor_antigo) end)::int as etapa,
       (case when operacao = 1 then relatorio.get_valor_campo_auditoria('componenteCurricular', '', valor_novo)
       else relatorio.get_valor_campo_auditoria('componenteCurricular', '', valor_antigo) end) as componente_curricular,
       relatorio.get_valor_campo_auditoria('nota', 'etapa', valor_antigo)::decimal as nota_antiga,
       relatorio.get_valor_campo_auditoria('nota', 'etapa', valor_novo)::decimal as nota_nova
from modules.auditoria;
ALTER TABLE relatorio.view_auditoria
  OWNER TO ieducar;

  -- undo

 DROP FUNCTION relatorio.get_valor_campo_auditoria(varchar, varchar, varchar);
 DROP VIEW relatorio.view_auditoria;