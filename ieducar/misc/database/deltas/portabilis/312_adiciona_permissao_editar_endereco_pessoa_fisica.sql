
-- @author   Caroline Salib Canto <caroline@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

insert into pmicontrolesis.menu values(999878,950,null,'Permitir editar endereço no cadastro de pessoa física',0,'','_self',1,7,1,null);
insert into portal.menu_submenu values(999878,55,0,'Permitir editar endereço no cadastro de pessoa física', '', null, 3);

-- Insere permissão para todos os tipos de usuário
insert into pmieducar.menu_tipo_usuario select cod_tipo_usuario, 999878, 1, 1, 1 from pmieducar.tipo_usuario;