  -- //

  --
  -- Altera tamanho da coluna 'nome' da tabela modules.componente_curricular
  --
  -- @author   Ricardo Bortolotto Dagostim <ricardo@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  ALTER TABLE modules.componente_curricular ALTER COLUMN nome type character varying(200);

  -- //@UNDO

  ALTER TABLE modules.componente_curricular ALTER COLUMN nome type character varying(100);

  -- //
