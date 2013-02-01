-- //

--
-- Atualiza campos no banco de dados, removendo restrições que não seguiam
-- a intenção do código.
--
-- @author   Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
-- @license  @@license@@
-- @version  $Id$
--

-- Etapa da média calculada
ALTER TABLE modules.nota_componente_curricular_media ADD COLUMN etapa character varying(2) NOT NULL;

-- Remove chave primária composta (apenas por DataMapper)
ALTER TABLE modules.falta_componente_curricular DROP CONSTRAINT falta_componente_curricular_pkey;

-- Chave primária simples
ALTER TABLE modules.falta_componente_curricular ADD CONSTRAINT falta_componente_curricular_pkey PRIMARY KEY (id);

-- Remove índice único
DROP INDEX modules.nota_componente_curricular_media_nota_aluno_key;

-- //@UNDO

-- Remove campo etapa
ALTER TABLE modules.nota_componente_curricular_media DROP COLUMN etapa;

-- Remove índice único
CREATE UNIQUE INDEX nota_componente_curricular_media_nota_aluno_key
  ON modules.nota_componente_curricular_media(nota_aluno_id);
  
-- Remove chave primária simples
ALTER TABLE modules.falta_componente_curricular DROP CONSTRAINT falta_componente_curricular_pkey;

-- Adiciona chave primária composta
ALTER TABLE modules.falta_componente_curricular ADD CONSTRAINT falta_componente_curricular_pkey PRIMARY KEY (falta_aluno_id, componente_curricular_id);

-- //