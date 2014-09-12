  -- 
  -- Remove índice que causava lentidão em algumas querys
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @author   Samuel Brognoli <samuel@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$


  DROP INDEX modules.idx_nota_componente_etp_aluno;

  -- //@UNDO

 CREATE INDEX idx_nota_componente_etp_aluno ON modules.nota_componente_curricular (componente_curricular_id , etapa , nota_aluno_id )

  -- //