-- @author   Caroline Salib Canto <caroline@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

ALTER TABLE portal.funcionario ADD COLUMN receber_novidades smallint;
ALTER TABLE portal.funcionario ADD COLUMN atualizou_cadastro smallint;
