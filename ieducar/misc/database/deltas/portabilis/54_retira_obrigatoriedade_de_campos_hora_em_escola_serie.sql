  -- //

  --
  -- Altera campos 'hora_inicial', 'hora_final', 'hora_inicio_intervalo', 'hora_fim_intervalo' para NULL
  --
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  ALTER TABLE pmieducar.escola_serie ALTER COLUMN hora_inicial drop not null;
  ALTER TABLE pmieducar.escola_serie ALTER COLUMN hora_final drop not null;
  ALTER TABLE pmieducar.escola_serie ALTER COLUMN hora_inicio_intervalo drop not null;
  ALTER TABLE pmieducar.escola_serie ALTER COLUMN hora_fim_intervalo drop not null;

  -- //@UNDO

  ALTER TABLE pmieducar.escola_serie ALTER COLUMN hora_inicial set not null;
  ALTER TABLE pmieducar.escola_serie ALTER COLUMN hora_final set not null;
  ALTER TABLE pmieducar.escola_serie ALTER COLUMN hora_inicio_intervalo set not null;
  ALTER TABLE pmieducar.escola_serie ALTER COLUMN hora_fim_intervalo set not null;

  -- //