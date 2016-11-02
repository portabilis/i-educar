-- //

--
-- Cria a chave primária na tabela modules.calendario_turma. O sql da tabela foi
-- gerado a partir de uma modelagem incompleta, que não continha as definições de
-- chave primária.
--
-- @author   Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
-- @license  @@license@@
-- @version  $Id$
--

ALTER TABLE "modules"."calendario_turma"
  ADD CONSTRAINT "calendario_turma_pk"
  PRIMARY KEY ("calendario_ano_letivo_id", "ano", "mes", "dia", "turma_id");

-- //@UNDO

-- SQL omitido intencionalmente. A tabela não é para ser criada sem a primary key.

-- //