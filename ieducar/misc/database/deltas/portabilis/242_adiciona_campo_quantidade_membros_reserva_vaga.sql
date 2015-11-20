  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  ALTER TABLE pmieducar.candidato_reserva_vaga ADD COLUMN quantidade_membros SMALLINT;

   -- UNDO

  ALTER TABLE pmieducar.candidato_reserva_vaga DROP COLUMN quantidade_membros;