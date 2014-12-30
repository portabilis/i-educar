-- 
-- @author   Isac Borgert <isac@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

insert into portal.menu_submenu values (21246, 69, 2 ,'Copia de Rotas', 'transporte_copia_rotas.php', null, 3);

insert into pmicontrolesis.menu values (21244, null, 20711, 'Processos', 0, null, '_self', 1, 17, 192);

insert into pmicontrolesis.menu values (21246, 21246, 21244, 'Copia de Rotas', 0, 'transporte_copia_rotas.php', '_self', 1, 17, 192);

insert into portal.menu_funcionario values(1,0,0,21246);

insert into pmieducar.menu_tipo_usuario values(1,21246,1,0,1);

