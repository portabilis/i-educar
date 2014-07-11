  -- //

  --
  -- Adiciona campos referentes à ocupação da pessoa:
  --   * Adicionado campo ocupação - referente à ocupação atual da pessoa
  --   * Adicionado campo empresa - nome da empresa em que a pessoa trabalha
  --   * Adicionado pessoa de contato na empresa
  --   * Adicionado telefone da empresa em que trabalha a pessoa
  --
  -- @author   Gabriel Matos de Souza <gabriel@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$
    
  ALTER TABLE cadastro.fisica ADD ocupacao VARCHAR(255);
  ALTER TABLE cadastro.fisica ADD empresa VARCHAR(255);
  ALTER TABLE cadastro.fisica ADD pessoa_contato VARCHAR(255);
  ALTER TABLE cadastro.fisica ADD ddd_telefone_empresa NUMERIC(3,0);
  ALTER TABLE cadastro.fisica ADD telefone_empresa NUMERIC(11,0);

  -- //@UNDO
    
  ALTER TABLE cadastro.fisica DROP COLUMN ocupacao VARCHAR(255);
  ALTER TABLE cadastro.fisica DROP COLUMN empresa VARCHAR(255);
  ALTER TABLE cadastro.fisica DROP COLUMN pessoa_contato VARCHAR(255);
  ALTER TABLE cadastro.fisica DROP COLUMN ddd_telefone_empresa NUMERIC(3,0);
  ALTER TABLE cadastro.fisica DROP COLUMN telefone_empresa NUMERIC(11,0);
    
  -- //