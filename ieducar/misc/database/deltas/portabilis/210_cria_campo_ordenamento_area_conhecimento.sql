--
-- @author   Alan Felipe Farias <caroline@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$
 -- Remove funções desnecessárias


   ALTER TABLE modules.area_conhecimento ADD COLUMN ordenamento_ac integer;
 
  -- //@UNDO

ALTER TABLE modules.area_conhecimento DROP COLUMN ordenamento_ac;
