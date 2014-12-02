  -- //

  --
  -- Cria o menu lateral e o menu suspenso para o módulo transporte escolar
  --
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @author   Ricardo Bortolotto <ricardo@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  INSERT INTO portal.menu_menu (cod_menu_menu, nm_menu)
  VALUES (69,'Transporte Escolar');

  INSERT INTO portal.menu_submenu (cod_menu_submenu, ref_cod_menu_menu, cod_sistema, nm_submenu, arquivo, nivel)
  VALUES (21234, 69, 2, 'Apresenta&ccedil;&atilde;o', 'transporte_index.php', '2');

  INSERT INTO pmicontrolesis.tutormenu (cod_tutormenu,nm_tutormenu)
  VALUES (17,'Transporte Escolar');

  INSERT INTO pmicontrolesis.menu (cod_menu,tt_menu,ord_menu,alvo,suprime_menu,ref_cod_tutormenu,ref_cod_ico)
  VALUES(20710,'Cadastros',1,'_self',1,17,1);

  INSERT INTO pmicontrolesis.menu (cod_menu,tt_menu,ord_menu,alvo,suprime_menu,ref_cod_tutormenu,ref_cod_ico)
  VALUES(20711,'Movimenta&ccedil;&atilde;o',2,'_self',1,17,1);

  INSERT INTO pmicontrolesis.menu (cod_menu,tt_menu,ord_menu,alvo,suprime_menu,ref_cod_tutormenu,ref_cod_ico)
  VALUES(20712,'Relat&oacute;rios',3,'_self',1,17,1);

  INSERT INTO portal.menu_submenu
  VALUES (21235, 69, 2, 'Empresas', 'transporte_empresa_lst.php',null,3);

  INSERT INTO portal.menu_submenu
  VALUES (21236, 69, 2, 'Motoristas', 'transporte_motorista_lst.php',null,3);

  INSERT INTO portal.menu_submenu
  VALUES (21237, 69, 2, 'Ve&iacute;culos', 'transporte_veiculo_lst.php',null,3);

  INSERT INTO portal.menu_submenu
  VALUES (21238, 69, 2, 'Rotas', 'transporte_rota_lst.php',null,3);

  INSERT INTO portal.menu_submenu
  VALUES (21239, 69, 2, 'Pontos', 'transporte_ponto_lst.php',null,3);

  INSERT INTO portal.menu_submenu
  VALUES (21240, 69, 2, 'Usu&aacute;rios de Transporte', 'transporte_pessoa_lst.php',null,3);

  INSERT INTO pmicontrolesis.menu
  VALUES(21235,21235,20710,'Empresas',1,'transporte_empresa_lst.php','_self',1,17,192);

  INSERT INTO pmicontrolesis.menu
  VALUES(21236,21236,20710,'Motoristas',2,'transporte_motorista_lst.php','_self',1,17,192);

  INSERT INTO pmicontrolesis.menu
  VALUES(21237,21237,20710,'Ve&iacute;culos',3,'transporte_veiculo_lst.php','_self',1,17,192);

  INSERT INTO pmicontrolesis.menu
  VALUES(21238,21238,20710,'Pontos',4,'transporte_ponto_lst.php','_self',1,17,192);

  INSERT INTO pmicontrolesis.menu
  VALUES(21239,21239,20710,'Rotas',5,'transporte_rota_lst.php','_self',1,17,192);

  INSERT INTO pmicontrolesis.menu
  VALUES(21240,21240,20711,'Usu&aacute;rios de Transporte',5,'transporte_pessoa_lst.php','_self',1,17,192);


  -- //@UNDO

  DELETE FROM pmicontrolesis.menu where cod_menu in (21235,21236,21237,21238,21239, 21240);
  DELETE FROM portal.menu_submenu where cod_menu_submenu in (21234,21235,21236,21237,21238,21239);
  DELETE FROM pmicontrolesis.menu where cod_menu in (20710,20711,20712);
  DELETE FROM pmicontrolesis.tutormenu where cod_tutormenu in (17);
  DELETE FROM portal.menu_submenu where ref_cod_menu_menu = 69;
  DELETE FROM portal.menu_menu where cod_menu_menu = 69;


  -- //
