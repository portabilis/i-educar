
-- @author   Caroline Salib Canto <caroline@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

insert into pmicontrolesis.menu values(999852, null, 21122, 'Bloqueio do ano letivo', 0, null, '_self', 1, 15, 19, null);

update pmicontrolesis.menu set ref_cod_menu_pai = 999852 where cod_menu = 21251;
update pmicontrolesis.menu set ref_cod_menu_pai = 999852 where cod_menu = 999848;