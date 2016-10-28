-- Cria menu para relatório de boletim de transferência
--
-- @author   Maurício Citadini Biléssimo <mauricio@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

insert into portal.menu_submenu values (999881, 55, 2,'Boletim de transferência', 'module/Reports/BoletimTransferencia', null, 3);
insert into pmicontrolesis.menu values (999881, 999881, 999450, 'Boletim de transferência', 0, 'module/Reports/BoletimTransferencia', '_self', 1, 15, 192, null);
insert into pmieducar.menu_tipo_usuario values(1,999881,1,1,1);