-- Cria menu para diário de exame final
-- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

insert into portal.menu_submenu values(999509,55,2,'Diário de exame final','module/Reports/DiarioExameFinal',NULL,3);
insert into pmicontrolesis.menu values(999509,999509,999500,'Diário de exame final',8,'module/Reports/DiarioExameFinal','_self',1,15,192);

--UNDO

  delete from pmicontrolesis.menu where cod_menu = 999509;
  delete from portal.menu_submenu where cod_menu_submenu = 999509;
