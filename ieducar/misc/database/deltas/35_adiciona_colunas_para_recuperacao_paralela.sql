
-- //

--
-- Adiciona colunas para controle de recuperação paralela
--
--
-- @author   Gabriel Matos de Souza <gabriel@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$
--
ALTER TABLE modules.regra_avaliacao ADD COLUMN tipo_recuperacao_paralela smallint DEFAULT 0;
ALTER TABLE modules.regra_avaliacao ADD COLUMN media_recuperacao_paralela numeric(5,3);
ALTER TABLE modules.nota_componente_curricular ADD COLUMN nota_recuperacao numeric(5,3);
ALTER TABLE modules.nota_componente_curricular ADD COLUMN nota_original numeric(5,3);

-- //@UNDO
ALTER TABLE modules.regra_avaliacao DROP COLUMN tipo_recuperacao_paralela;
ALTER TABLE modules.regra_avaliacao DROP COLUMN media_recuperacao_paralela;
ALTER TABLE modules.nota_componente_curricular DROP COLUMN nota_recuperacao numeric(5,3);
ALTER TABLE modules.nota_componente_curricular DROP COLUMN nota_original numeric(5,3);