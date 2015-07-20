--
-- @author   Caroline Salib Canto <caroline@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$


alter table modules.componente_curricular_turma add column etapas_especificas smallint;
alter table modules.componente_curricular_turma add column etapas_utilizadas varchar;

  -- undo

alter table modules.componente_curricular_turma drop column etapas_especificas;
alter table modules.componente_curricular_turma drop column etapas_utilizadas;
