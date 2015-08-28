--
-- @author   Alan Felipe Farias <caroline@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$
ALTER TABLE modules.area_conhecimento DROP COLUMN ordenamento_ac;
ALTER TABLE modules.area_conhecimento ADD COLUMN ordenamento_ac INTEGER DEFAULT 99999;
 
  -- //@UNDO

ALTER TABLE modules.area_conhecimento DROP COLUMN ordenamento_ac;
