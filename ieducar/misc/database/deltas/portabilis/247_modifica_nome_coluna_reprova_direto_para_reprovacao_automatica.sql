  -- @author   Gabriel Matos de Souza <gabriel@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

ALTER TABLE modules.regra_avaliacao RENAME COLUMN reprova_direto TO reprovacao_automatica;

-- UNDO

ALTER TABLE modules.regra_avaliacao RENAME COLUMN reprovacao_automatica TO reprova_direto;