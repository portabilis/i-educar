  -- @author   Gabriel Matos de Souza <gabriel@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

alter table pmicontrolesis.menu alter column tt_menu type varchar(255);
alter table portal.menu_submenu alter column nm_submenu type varchar(255);
insert into pmicontrolesis.menu values(630,950,null,'Alterar média final do aluno e situação (para regras de avaliação manuais)',0,'','_self',1,7,1,null);
insert into portal.menu_submenu values(630,55,0,'Alterar média final do aluno e situação (para regras de avaliação manuais)', '', null, 3);
insert into pmieducar.menu_tipo_usuario values(1, 630, 1, 1, 1);