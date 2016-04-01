--Cria campo para professor que lecionam uma disciplina específica

--@author   Gabriel Souza <gabriel@portabilis.com.br>

alter table modules.professor_turma add column area_especifica integer default 0;

--@UNDO

alter table modules.professor_turma drop column area_especifica;