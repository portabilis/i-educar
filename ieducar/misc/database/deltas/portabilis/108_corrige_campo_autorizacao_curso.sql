  -- //

  --
  -- Muda o campo autorização da tabela curso para a tabela escola_curso
  --
  -- @author   Gabriel Matos de Souza <gabriel@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$
    
    ALTER TABLE pmieducar.curso DROP COLUMN autorizacao;

    ALTER TABLE pmieducar.escola_curso ADD autorizacao varchar(255);

  -- //@UNDO
    
    ALTER TABLE pmieducar.curso ADD autorizacao varchar(255);

    ALTER TABLE pmieducar.escola_curso DROP COLUMN autorizacao;
    
  -- //