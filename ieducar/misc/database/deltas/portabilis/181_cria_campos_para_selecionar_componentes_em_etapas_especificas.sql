--
-- @author   Gabriel Matos de Souza <gabriel@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$


alter table pmieducar.escola_serie_disciplina add column etapas_especificas smallint;
alter table pmieducar.escola_serie_disciplina add column etapas_utilizadas varchar;

  -- undo

alter table pmieducar.escola_serie_disciplina drop column etapas_especificas;
alter table pmieducar.escola_serie_disciplina drop column etapas_utilizadas;