-- Cria menu Ficha individual - EJA
-- @author Paula Bonot <bono@portabilis.com.br>

INSERT INTO portal.menu_submenu VALUES(999876,55,2,'Ficha individual - EJA','module/Reports/FichaIndividualEJA',NULL,3);
INSERT INTO pmicontrolesis.menu VALUES(999876,999876,999450,'Ficha individual - EJA',0,'module/Reports/FichaIndividualEJA','_self',1,15,192);
INSERT INTO pmieducar.menu_tipo_usuario VALUES(1,999876,1,1,1);

--UNDO

DELETE FROM pmieducar.menu_tipo_usuario WHERE ref_cod_menu_submenu = 999876;
DELETE FROM portal.menu_submenu WHERE cod_menu_submenu = 999876;
DELETE FROM pmicontrolesis.menu WHERE cod_menu = 999876;