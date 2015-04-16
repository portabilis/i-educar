  -- //
  
  --
  -- Cria menu para canhotos do professor regente e da área
  -- @author   Gabriel Matos de Souza <gabriel@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$  
  
  insert into pmicontrolesis.menu values(999451, null, 21127, 'Canhotos', 0, null, '_self', 1, 15, 19, null);

  insert into portal.menu_submenu values(999818,55,2,'Canhoto do professor','module/Reports/CanhotoProfessor',NULL,3);
  insert into pmicontrolesis.menu values(999818,999818,999451,'Canhoto do professor',0,'module/Reports/CanhotoProfessor','_self',1,15,192);

