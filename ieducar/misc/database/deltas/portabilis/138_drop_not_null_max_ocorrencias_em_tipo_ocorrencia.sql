  -- 
  -- Retira obrigatoriedade do campo máximo de ocorrências em tipo de ocorrência
  -- @author   Gabriel Matos de Souza <gabriel@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  ALTER TABLE pmieducar.tipo_ocorrencia_disciplinar ALTER COLUMN max_ocorrencias DROP NOT NULL;

  -- //