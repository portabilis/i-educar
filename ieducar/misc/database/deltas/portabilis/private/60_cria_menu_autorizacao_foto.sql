  -- //
  
  --
  -- Cria menu para Autorização de Foto de Menor
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$  
  
  
  insert into portal.menu_submenu values(999226,55,2,'Utilização da imagem do Aluno','module/Reports/AutorizacaoAluno',NULL,3);
  insert into pmicontrolesis.menu values(999226,999226,999400,'Utilização da imagem do Aluno',5,'module/Reports/AutorizacaoAluno','_self',1,15,192);

  -- //@UNDO
  
  delete from pmicontrolesis.menu where cod_menu = 999226;
  delete from portal.menu_submenu where cod_menu_submenu = 999226;
  
  -- //
