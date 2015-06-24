--
-- @author   Caroline Salib Canto <caroline@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

-- Novo schema para tratamento dos relatórios

CREATE SCHEMA relatorio;

-- Tabela de tipo de situacoes

CREATE TABLE relatorio.situacao_matricula
(
  cod_situacao integer NOT NULL,
  descricao character varying(50) NOT NULL,
  CONSTRAINT situacao_matricula_pkey PRIMARY KEY (cod_situacao)
)
WITH (
  OIDS=TRUE
);
ALTER TABLE relatorio.situacao_matricula
  OWNER TO ieducar;

insert into relatorio.situacao_matricula (cod_situacao, descricao) values (1, 'Aprovado');
insert into relatorio.situacao_matricula (cod_situacao, descricao) values (2, 'Reprovado');
insert into relatorio.situacao_matricula (cod_situacao, descricao) values (3, 'Em andamento');
insert into relatorio.situacao_matricula (cod_situacao, descricao) values (4, 'Transferido');
insert into relatorio.situacao_matricula (cod_situacao, descricao) values (6, 'Abandono');
insert into relatorio.situacao_matricula (cod_situacao, descricao) values (7, 'Reclassificado');
insert into relatorio.situacao_matricula (cod_situacao, descricao) values (9, 'Exceto Transferidos/Abandono');
insert into relatorio.situacao_matricula (cod_situacao, descricao) values (10, 'Todas');

-- View de situacoes

CREATE OR REPLACE VIEW relatorio.view_situacao AS 
 SELECT matricula.cod_matricula, situacao_matricula.cod_situacao, matricula_turma.ref_cod_turma AS cod_turma, matricula_turma.sequencial
   FROM relatorio.situacao_matricula, pmieducar.matricula
   JOIN pmieducar.matricula_turma ON matricula_turma.ref_cod_matricula = matricula.cod_matricula
  WHERE 
   CASE
       WHEN matricula.aprovado = 4 THEN matricula_turma.ativo = 1 OR (EXISTS ( SELECT 1
          FROM pmieducar.transferencia_solicitacao
         WHERE pmieducar.transferencia_solicitacao.ref_cod_matricula_saida = matricula.cod_matricula AND transferencia_solicitacao.ativo = 1
        LIMIT 1)) AND matricula_turma.sequencial = (( SELECT max(matricula_turma.sequencial) AS max
          FROM pmieducar.matricula_turma
         WHERE matricula_turma.ref_cod_matricula = matricula.cod_matricula)) OR matricula_turma.transferido OR matricula_turma.reclassificado
       WHEN matricula.aprovado = 6 THEN matricula_turma.ativo = 1 OR matricula_turma.sequencial = (( SELECT max(matricula_turma.sequencial) AS max
          FROM pmieducar.matricula_turma
         WHERE matricula_turma.ref_cod_matricula = matricula.cod_matricula)) OR matricula_turma.abandono
       ELSE matricula_turma.ativo = 1 OR matricula_turma.abandono OR matricula_turma.reclassificado OR matricula_turma.transferido OR matricula_turma.remanejado
   END AND 
   CASE
       WHEN situacao_matricula.cod_situacao = 10 THEN matricula.aprovado = ANY (ARRAY[1::smallint, 2::smallint, 3::smallint, 4::smallint, 5::smallint, 6::smallint])
       WHEN situacao_matricula.cod_situacao = 7 THEN matricula.aprovado = 5::smallint
       WHEN situacao_matricula.cod_situacao = 9 THEN (matricula.aprovado = ANY (ARRAY[1::smallint, 2::smallint, 3::smallint, 5::smallint])) AND (NOT matricula_turma.reclassificado OR matricula_turma.reclassificado IS NULL) AND (NOT matricula_turma.abandono OR matricula_turma.abandono IS NULL) AND (NOT matricula_turma.remanejado OR matricula_turma.remanejado IS NULL) AND (NOT matricula_turma.transferido OR matricula_turma.transferido IS NULL)
       WHEN situacao_matricula.cod_situacao = ANY (ARRAY[1, 2, 3]) THEN matricula.aprovado = situacao_matricula.cod_situacao AND (NOT matricula_turma.reclassificado OR matricula_turma.reclassificado IS NULL) AND (NOT matricula_turma.abandono OR matricula_turma.abandono IS NULL) AND (NOT matricula_turma.remanejado OR matricula_turma.remanejado IS NULL) AND (NOT matricula_turma.transferido OR matricula_turma.transferido IS NULL)
       ELSE matricula.aprovado = situacao_matricula.cod_situacao
   END;

ALTER TABLE relatorio.view_situacao
  OWNER TO ieducar;


-- Função para tirar o caracter especial do campo enviado via parâmetro

CREATE OR REPLACE FUNCTION relatorio.get_texto_sem_caracter_especial(character varying)
  RETURNS character varying AS
$BODY$SELECT translate(public.fcn_upper($1),
'åáàãâäéèêëíìîïóòõôöúùüûçÿýñÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ', 
'aaaaaaeeeeiiiiooooouuuucyynAAAAAAEEEEIIIIOOOOOUUUUCYN');$BODY$
  LANGUAGE sql VOLATILE;
ALTER FUNCTION relatorio.get_texto_sem_caracter_especial(character varying)
  OWNER TO ieducar;

CREATE OR REPLACE FUNCTION relatorio.get_texto_situacao(integer)
  RETURNS character varying AS
$BODY$SELECT CASE
 WHEN matricula_turma.remanejado  THEN 'Remanejado'
 WHEN matricula_turma.transferido THEN 'Transferido'
 WHEN matricula_turma.reclassificado THEN 'Recuperação'
 WHEN matricula_turma.abandono THEN
 (select coalesce(abandono_tipo.nome, 'Abandono')
    from pmieducar.abandono_tipo
   where matricula.ref_cod_abandono_tipo = abandono_tipo.cod_abandono_tipo
     and abandono_tipo.ativo = 1 limit 1)
 WHEN matricula.aprovado = 1 THEN 'Aprovado'
 WHEN matricula.aprovado = 2 THEN 'Reprovado'
 WHEN matricula.aprovado = 3 THEN 'Andamento'
 WHEN matricula.aprovado = 4 THEN 'Transferido'
 WHEN matricula.aprovado = 5 THEN 'Reclassificado'
 WHEN matricula.aprovado = 6 THEN
 (select coalesce(abandono_tipo.nome, 'Abandono')
    from pmieducar.abandono_tipo
   where matricula.ref_cod_abandono_tipo = abandono_tipo.cod_abandono_tipo
     and abandono_tipo.ativo = 1 limit 1)
 ELSE 'Rec' END
 FROM pmieducar.matricula
 INNER JOIN pmieducar.matricula_turma ON (matricula_turma.ref_cod_matricula = matricula.cod_matricula)
 WHERE matricula.cod_matricula = $1;$BODY$
  LANGUAGE sql VOLATILE;
ALTER FUNCTION relatorio.get_texto_situacao(integer)
  OWNER TO ieducar;

-- Função que retorna o texto da situação da matrícula enviada via parâmetro de forma abreviada.

CREATE OR REPLACE FUNCTION relatorio.get_texto_situacao_simplificado(integer)
  RETURNS character varying AS
$BODY$SELECT CASE
 WHEN matricula_turma.remanejado  THEN 'Rem'
 WHEN matricula_turma.transferido THEN 'Trs'
 WHEN matricula_turma.reclassificado THEN 'Rec'
 WHEN matricula_turma.abandono THEN
 (select coalesce(abandono_tipo.nome, 'Aba')
    from pmieducar.abandono_tipo
   where matricula.ref_cod_abandono_tipo = abandono_tipo.cod_abandono_tipo
     and abandono_tipo.ativo = 1 limit 1)
 WHEN matricula.aprovado = 1 THEN 'Apr'
 WHEN matricula.aprovado = 2 THEN 'Rep'
 WHEN matricula.aprovado = 3 THEN 'And'
 WHEN matricula.aprovado = 4 THEN 'Trs'
 WHEN matricula.aprovado = 5 THEN 'Recl'
 WHEN matricula.aprovado = 6 THEN
 (select coalesce(abandono_tipo.nome, 'Aba')
    from pmieducar.abandono_tipo
   where matricula.ref_cod_abandono_tipo = abandono_tipo.cod_abandono_tipo
     and abandono_tipo.ativo = 1 limit 1)
 ELSE 'Rec' END
 FROM pmieducar.matricula
 INNER JOIN pmieducar.matricula_turma ON (matricula_turma.ref_cod_matricula = matricula.cod_matricula)
 WHERE matricula.cod_matricula = $1;$BODY$
  LANGUAGE sql VOLATILE;
ALTER FUNCTION relatorio.get_texto_situacao_simplificado(integer)
  OWNER TO ieducar;
-- undo

DROP VIEW relatorio.view_situacao;
DROP TABLE pmieducar.situacao_matricula;
DROP FUNCTION relatorio.get_texto_sem_caracter_especial(character varying);
DROP FUNCTION relatorio.get_texto_situacao(integer);
DROP FUNCTION relatorio.get_texto_situacao_simplificado(integer);
DROP SCHEMA relatorio;