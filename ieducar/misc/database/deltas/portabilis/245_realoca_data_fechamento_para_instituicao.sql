  -- @author   Alan Felipe Farias <alan@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

ALTER TABLE pmieducar.instituicao ADD data_fechamento date;
UPDATE pmieducar.instituicao SET data_fechamento = (SELECT DATA_FECHAMENTO FROM PMIEDUCAR.TURMA WHERE DATA_FECHAMENTO >1 LIMIT 1) WHERE cod_instituicao=1;
ALTER TABLE pmieducar.turma DROP COLUMN data_fechamento;