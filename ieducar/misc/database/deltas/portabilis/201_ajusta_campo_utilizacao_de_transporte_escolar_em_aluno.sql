  --
  -- Insere informações de transporte escolar em alunos que não possuem informação cadastrada
  -- @author   Gabriel Matos de Souza <gabriel@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  insert into modules.transporte_aluno (aluno_id, responsavel, user_id, created_at, updated_at)
  ((select cod_aluno,  0, 1, CURRENT_DATE, CURRENT_DATE 
      from pmieducar.aluno 
     where cod_aluno not in(select aluno_id 
      from modules.transporte_aluno)));
  -- //