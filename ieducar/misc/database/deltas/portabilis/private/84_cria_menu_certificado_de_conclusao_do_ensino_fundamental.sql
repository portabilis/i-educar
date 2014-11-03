--
-- Cria o menu para o certificado de conclusão do ensino fundamental
-- @author   Gabriel Matos de Souza <gabriel@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$


insert into portal.menu_submenu values( 999808, 55, 2, 'Certificado de Conclusão do Ensino Fundamental', 'module/Reports/CertificadoEnsinoFundamental', null, 3);
insert into pmicontrolesis.menu values( 999807, null, 21127, 'Certificados', 3, null, '_self', 1, 15, 25);
insert into pmicontrolesis.menu values( 999808, 999808, 999807, 'Certificado de Conclusão do Ensino Fundamental', 1, 'module/Reports/CertificadoEnsinoFundamental', '_self', 1, 15, 122);

-- //@UNDO

delete from pmicontrolesis.menu where cod_menu in(999807,999808);
delete from portal.menu_submenu where cod_menu_submenu = 999808;