-- //

--
-- Adiciona os campos registro, livro e folha no histórico escolar do aluno,
-- para informar os respectivos dados em que o histórico foi arquivado.
--
-- @author   Lucas D'Avila <lucasdavila@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$
--

ALTER TABLE pmieducar.historico_escolar ADD COLUMN registro character varying(50);
ALTER TABLE pmieducar.historico_escolar ADD COLUMN livro character varying(50);
ALTER TABLE pmieducar.historico_escolar ADD COLUMN folha character varying(50);

-- //@UNDO

ALTER TABLE pmieducar.historico_escolar DROP COLUMN registro;
ALTER TABLE pmieducar.historico_escolar DROP COLUMN livro;
ALTER TABLE pmieducar.historico_escolar DROP COLUMN folha;

-- //
