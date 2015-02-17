
-- @author   Isac Borgert <isac@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$


insert into portal.menu_submenu values (21249, 69, 2 ,'Alunos do transporte escolar', 'module/Reports/AlunosTransporteKm', null, 3);

insert into pmicontrolesis.menu values (21249, 21249, 20712, 'Alunos do transporte escolar', 0, 'module/Reports/AlunosTransporteKm', '_self', 1, 17, 192);

insert into portal.menu_funcionario values(1,0,0,21249);

insert into pmieducar.menu_tipo_usuario values(1,21249,1,0,1);