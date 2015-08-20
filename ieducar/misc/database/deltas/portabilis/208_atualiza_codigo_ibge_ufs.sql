--
-- @author   Alan Felipe Farias <alan@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

ALTER TABLE  public.uf ALTER COLUMN sigla_uf TYPE varchar(3);

  -- undo

ALTER TABLE  public.uf ALTER COLUMN sigla_uf TYPE varchar(2);