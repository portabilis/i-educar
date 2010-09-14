-- //

--
-- Adiciona o campo zona_localizacao nas tabelas de endereçamento
-- cadastro.endereco_externo e public.bairro.
--
-- @author   Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
-- @license  @@license@@
-- @version  $Id$
--

ALTER TABLE cadastro.endereco_externo ADD COLUMN zona_localizacao integer DEFAULT 1;
ALTER TABLE public.bairro ADD COLUMN zona_localizacao integer DEFAULT 1;

DROP VIEW cadastro.v_endereco;

CREATE OR REPLACE VIEW cadastro.v_endereco AS
  SELECT
    e.idpes, e.cep, e.idlog, e.numero, e.letra, e.complemento, e.idbai, e.bloco, e.andar, e.apartamento, l.nome AS logradouro, l.idtlog, b.nome AS bairro, m.nome AS cidade, m.sigla_uf, b.zona_localizacao
  FROM
    endereco_pessoa e, logradouro l, bairro b, municipio m
  WHERE
    e.idlog = l.idlog AND e.idbai = b.idbai AND b.idmun = m.idmun AND e.tipo = 1::numeric
  UNION
  SELECT
    e.idpes, e.cep, NULL::"unknown" AS idlog, e.numero, e.letra, e.complemento, NULL::"unknown" AS idbai, e.bloco, e.andar, e.apartamento, e.logradouro, e.idtlog, e.bairro, e.cidade, e.sigla_uf, e.zona_localizacao
  FROM
    endereco_externo e
  WHERE
    e.tipo = 1::numeric;

-- //@UNDO

DROP VIEW cadastro.v_endereco;

CREATE OR REPLACE VIEW cadastro.v_endereco AS
  SELECT
    e.idpes, e.cep, e.idlog, e.numero, e.letra, e.complemento, e.idbai, e.bloco, e.andar, e.apartamento, l.nome AS logradouro, l.idtlog, b.nome AS bairro, m.nome AS cidade, m.sigla_uf
  FROM
    endereco_pessoa e, logradouro l, bairro b, municipio m
  WHERE
    e.idlog = l.idlog AND e.idbai = b.idbai AND b.idmun = m.idmun AND e.tipo = 1::numeric
  UNION
  SELECT
    e.idpes, e.cep, NULL::"unknown" AS idlog, e.numero, e.letra, e.complemento, NULL::"unknown" AS idbai, e.bloco, e.andar, e.apartamento, e.logradouro, e.idtlog, e.bairro, e.cidade, e.sigla_uf
  FROM
    endereco_externo e
  WHERE
    e.tipo = 1::numeric;

ALTER TABLE cadastro.endereco_externo DROP COLUMN zona_localizacao;
ALTER TABLE public.bairro DROP COLUMN zona_localizacao;

-- //