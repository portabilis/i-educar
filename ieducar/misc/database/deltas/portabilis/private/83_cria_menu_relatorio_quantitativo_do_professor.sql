--
-- Cria o menu para o relatório quantitativo de professores.
-- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$


insert into portal.menu_submenu values(999239, 55, 2, 'Relatório quantitativo de professores', 'module/Reports/QuantitativoProfessores', null, 3);
insert into pmicontrolesis.menu values(999239, 999239, 999300, 'Relatório quantitativo de professores', 5, 'module/Reports/QuantitativoProfessores', '_self', 1, 15, 192);

-- //@UNDO

delete from pmicontrolesis.menu where cod_menu = 999239;
delete from portal.menu_submenu where cod_menu_submenu = 999239;