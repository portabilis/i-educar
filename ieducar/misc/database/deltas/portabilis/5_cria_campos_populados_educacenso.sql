 	-- //

 	--
 	-- Popula as tabelas escola_localizacao, cadastro.deficiencia ,   
 	-- modules.educacenso_cod_turma.
 	-- @author   Ricardo Bortolotto Dagostim <ricardo@portabilis.com.br>
 	-- @license  @@license@@
 	-- @version  $Id$
 	--

	insert into pmieducar.escola_localizacao values(1,NULL,1,'Urbana',current_timestamp,NULL,1,1);
	insert into pmieducar.escola_localizacao values(2,NULL,1,'Rural',current_timestamp,NULL,1,1);
    --
	insert into cadastro.deficiencia values(1,'Nenhuma');
	insert into cadastro.deficiencia values(2,'Cegueira');
	insert into cadastro.deficiencia values(3,'Baixa Visão');
	insert into cadastro.deficiencia values(4,'Surdez');
	insert into cadastro.deficiencia values(5,'Deficiência Auditiva');
	insert into cadastro.deficiencia values(6,'Surdocegueira');
	insert into cadastro.deficiencia values(7,'Deficiência Física');
	insert into cadastro.deficiencia values(8,'Deficiência Mental');
	insert into cadastro.deficiencia values(9,'Deficiência Múltipla');
	insert into cadastro.deficiencia values(10,'Autismo Clássico');
	insert into cadastro.deficiencia values(11,'Síndrome de Asperger');
	insert into cadastro.deficiencia values(12,'Síndrome de Rett');
	insert into cadastro.deficiencia values(13,'Transtorno desintegrativo da infância (psicose infantil)');
	insert into cadastro.deficiencia values(14,'Altas Habilidades/Superdotação');

	--
	
	insert into cadastro.raca values(1,NULL,1,'Branca',current_timestamp,NULL,'t');
	insert into cadastro.raca values(2,NULL,1,'Preta',current_timestamp,NULL,'t');
	insert into cadastro.raca values(3,NULL,1,'Parda',current_timestamp,NULL,'t');
	insert into cadastro.raca values(4,NULL,1,'Amarela',current_timestamp,NULL,'t');
	insert into cadastro.raca values(5,NULL,1,'Indígena',current_timestamp,NULL,'t');
	insert into cadastro.raca values(6,NULL,1,'Não Declarada',current_timestamp,NULL,'t');


	--

	insert into cadastro.escolaridade values(1,'Fundamental Incompleto');
	insert into cadastro.escolaridade values(2,'Fundamental Completo');
	insert into cadastro.escolaridade values(3,'Ensino Médio (Normal/Magistério)');
	insert into cadastro.escolaridade values(4,'Ensino Médio (Normal/Magistério Indígena)');
	insert into cadastro.escolaridade values(5,'Ensino Médio');
	insert into cadastro.escolaridade values(6,'Superior Completo');

 	-- //@UNDO
	
    delete from pmieducar.escola_localizacao where cod_escola_localizacao in(1,2);
	
	delete from cadastro.deficiencia where cod_deficiencia in(1,2,3,4,5,6,7,8,9,10,11,12,13,14);
	
 	delete from cadastro.raca where cod_raca in(1,2,3,4,5,6);
	
 	delete from cadastro.escolaridade where idesco in(1,2,3,4,5,6);

 	-- //		