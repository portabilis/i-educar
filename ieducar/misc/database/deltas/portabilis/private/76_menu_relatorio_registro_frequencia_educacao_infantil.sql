  -- //

  --
  -- Cria menu para o registro de frequência anos iniciais da educação infantil
  -- @author   Gabriel Matos de Souza <gabriel@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$


  insert into portal.menu_submenu values(999236, 55, 2, 'Registro de Frequência - Anos Iniciais Educ Infantil', 'module/Reports/RegistroFrequenciaAnosIniciaisEducInfantil', NULL, 3);
  insert into pmicontrolesis.menu values(999236, 999236, 999500, 'Registro de Frequência - Anos Iniciais Educ. Infantil', 5, 'module/Reports/RegistroFrequenciaAnosIniciaisEducInfantil', '_self', 1, 15, 192)


  -- //@UNDO

  delete from pmicontrolesis.menu where cod_menu = 999236;
  delete from portal.menu_submenu where cod_menu_submenu = 999236;

  -- //
