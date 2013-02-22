-- //

--
-- Adiciona o campo frequencia escolar a tabela historico_escolar.
--
-- @author   Lucas D'Avila <lucasdavila@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$
--

  ALTER TABLE pmieducar.historico_escolar ADD COLUMN frequencia decimal(5,2) DEFAULT 0.000;

-- //@UNDO

  ALTER TABLE pmieducar.historico_escolar DROP COLUMN frequencia;

--