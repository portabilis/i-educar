  -- //

  --
  -- Adiciona campos situacao e referência da matrícula na tabela dos candidatos a reserva de vaga
  --
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  ALTER TABLE pmieducar.candidato_reserva_vaga
  ADD COLUMN ref_cod_matricula INTEGER;
  ALTER TABLE pmieducar.candidato_reserva_vaga
  ADD COLUMN situacao CHAR(1);
  ALTER TABLE pmieducar.candidato_reserva_vaga
  ADD COLUMN data_situacao date;

  -- //