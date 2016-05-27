-- Cria menu categoria de obras
-- @author Maurício Citadini Biléssimo <mauricio@portabilis.com.br>

insert into pmicontrolesis.menu values(999866, 598, 15858, 'Categoria', 0, 'educar_categoria_lst.php', '_self', 1, 16, 1, null);
insert into portal.menu_submenu values(999867,57,2,'Categoria de obras','educar_categoria_lst.php',NULL,3);
insert into pmicontrolesis.menu values(999867,999867,999866,'Categoria de obras',0,'educar_categoria_lst.php','_self',1,16,1);
insert into pmieducar.menu_tipo_usuario values(1,999867,1,1,1);