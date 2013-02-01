 	-- //
  
 	--
 	-- Cria os menus para o sistema de Biblioteca
	-- @author   Ricardo Bortolotto Dagostim <ricardo@portabilis.com.br>
 	-- @license  @@license@@
 	-- @version  $Id$
  
  
  insert into pmicontrolesis.menu values(15880,591,15858,'Biblioteca',1,'educar_biblioteca_lst.php','_self',1,16,1);
  insert into pmicontrolesis.menu values(15881,594,15858,'Autores',2,'educar_acervo_autor_lst.php','_self',1,16,141);
  insert into pmicontrolesis.menu values(15882,593,15858,'Coleção',3,'educar_acervo_colecao_lst.php','_self',1,16,119);
  insert into pmicontrolesis.menu values(15883,595,15858,'Editora',4,'educar_acervo_editora_lst.php','_self',1,16,176);
  insert into pmicontrolesis.menu values(15884,590,15858,'Idioma',5,'educar_acervo_idioma_lst.php','_self',1,16,26);
  insert into pmicontrolesis.menu values(15885,597,15858,'Tipo de Exemplar',7,'educar_exemplar_tipo_lst.php','_self',1,16,177);
  insert into pmicontrolesis.menu values(15886,596,15858,'Tipo de Cliente',8,'educar_cliente_tipo_lst.php','_self',1,16,62);
  insert into pmicontrolesis.menu values(15887,600,15858,'Motivo Baixa',9,'educar_motivo_baixa_lst.php','_self',1,16,1);
  insert into pmicontrolesis.menu values(15888,607,15858,'Motivo Suspensão',10,'educar_motivo_suspensao_lst.php','_self',1,16,1);
  insert into pmicontrolesis.menu values(15889,608,15858,'Fonte',11,'educar_fonte_lst.php','_self',1,16,1);
  insert into pmicontrolesis.menu values(15890,629,15858,'Dados Biblioteca',12,'educar_biblioteca_dados_lst.php','_self',1,16,143);
  insert into pmicontrolesis.menu values(15891,602,15858,'Situação Exemplar',13,'educar_situacao_lst.php','_self',1,16,1);
  insert into pmicontrolesis.menu values(15892,603,15859,'Cliente',1,'educar_cliente_lst.php','_self',1,16,1);
  insert into pmicontrolesis.menu values(15893,622,15859,'Dívidas',2,'educar_pagamento_multa_lst.php','_self',1,16,1);
  insert into pmicontrolesis.menu values(15894,610,15859,'Empréstimo',3,'educar_exemplar_emprestimo_lst.php','_self',1,16,1);
  insert into pmicontrolesis.menu values(15895,628,15859,'Devolução',4,'educar_exemplar_devolucao_lst.php','_self',1,16,1);
  
	-- //@UNDO
  
  delete from pmicontrolesis.menu where cod_menu in(15880,15881,15882,15883,15884,15885,15886,15887,15888,15889,15890,15891,15892,15893,15894,15895);
  
 
	-- //