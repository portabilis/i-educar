  -- //

  --
  -- Cria campo para armazenar URL do laudo médico no cadastro do aluno.
  --
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$
    
    ALTER TABLE pmieducar.aluno ADD COLUMN url_laudo_medico character varying(255);

  -- //@UNDO
    
    ALTER TABLE pmieducar.aluno DROP COLUMN url_laudo_medico;
    
  -- //