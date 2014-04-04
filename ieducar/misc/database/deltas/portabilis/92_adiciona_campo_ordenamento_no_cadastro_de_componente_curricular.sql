  -- //

  --
  -- Adiciona o campo de ordenamento no cadastro de componente curricular para realizar ordenação na hora de inserir
  -- faltas/notas de um aluno
  --
  -- @author   Gabriel Matos de Souza <gabriel@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  ALTER TABLE modules.componente_curricular ADD ordenamento INTEGER DEFAULT 99999;

  -- //@UNDO

  ALTER TABLE modules.componente_curricular DROP ordenamento;

  -- //