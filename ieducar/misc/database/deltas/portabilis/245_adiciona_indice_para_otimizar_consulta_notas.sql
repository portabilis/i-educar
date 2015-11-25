  -- @author   Gabriel Matos de Suuza <gabriel@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

CREATE INDEX idx_nota_componente_curricular_etapa
ON modules.nota_componente_curricular (nota_aluno_id, componente_curricular_id, etapa);

-- UNDO

DROP INDEX idx_nota_componente_curricular_etapa;