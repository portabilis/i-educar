-- //

--
-- Atualiza campos no banco de dados, removendo restrição incorreta da tabela.
--
-- @author   Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
-- @license  @@license@@
-- @version  $Id$
--

-- Remove chave primária incorreta
ALTER TABLE modules.falta_geral DROP CONSTRAINT falta_geral_pkey;

-- Chave primária simples
ALTER TABLE modules.falta_geral ADD CONSTRAINT falta_geral_pkey PRIMARY KEY (id);

-- //@UNDO

-- Adiciona chave primária composta
ALTER TABLE modules.falta_geral ADD CONSTRAINT falta_geral_pkey PRIMARY KEY (falta_aluno_id);

-- //