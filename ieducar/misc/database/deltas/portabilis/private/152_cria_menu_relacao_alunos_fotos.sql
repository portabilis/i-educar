-- Relatórico de alunos com fotos
-- @author Paula Bonot <bono@portabilis.com.br>

INSERT INTO portal.menu_submenu VALUES(999879,55,2,'Relação de alunos com fotos','module/Reports/AlunosFotos',NULL,3);
INSERT INTO pmicontrolesis.menu VALUES(999879,999879,999300,'Relação de alunos com fotos',0,'module/Reports/AlunosFotos','_self',1,15,192);
INSERT INTO pmieducar.menu_tipo_usuario VALUES(1,999879,1,1,1);

--UNDO

DELETE FROM pmieducar.menu_tipo_usuario WHERE ref_cod_menu_submenu = 999879;
DELETE FROM portal.menu_submenu WHERE cod_menu_submenu = 999879;
DELETE FROM pmicontrolesis.menu WHERE cod_menu = 999879;