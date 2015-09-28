--
-- @author   Alan Felipe Farias <alan@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$


ALTER TABLE pmieducar.serie ADD idade_ideal integer;

-- undo
ALTER TABLE pmieducar.serie drop column idade_ideal;