  -- //
  -- @author   Gabriel Matos de Souza <gabriel@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$
    
  
  ALTER TABLE pmieducar.instituicao ADD controlar_espaco_utilizacao_aluno SMALLINT;
  ALTER TABLE pmieducar.instituicao ADD percentagem_maxima_ocupacao_salas NUMERIC(5,2);
  ALTER TABLE pmieducar.instituicao ADD quantidade_alunos_metro_quadrado INTEGER;

  -- //@UNDO
    
  ALTER TABLE pmieducar.instituicao DROP controlar_espaco_utilizacao_aluno;
  ALTER TABLE pmieducar.instituicao DROP percentagem_maxima_ocupacao_salas;
  ALTER TABLE pmieducar.instituicao DROP quantidade_alunos_metro_quadrado;
    
  -- //