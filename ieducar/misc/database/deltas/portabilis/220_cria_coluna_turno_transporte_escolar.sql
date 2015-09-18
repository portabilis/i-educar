--
-- @author   Alan Felipe Farias <alan@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$


ALTER TABLE modules.pessoa_transporte ADD turno varchar(255);

-- undo

ALTER TABLE modules.pessoa_transporte drop column turno;