  -- 
  -- Cria professor multi seriado inserido no cadastro de servidores para permitir alocações no mesmo horário
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  ALTER TABLE pmieducar.servidor ADD COLUMN  multi_seriado boolean;

  -- //