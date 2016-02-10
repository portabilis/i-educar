-- @author   Paula Bonot <bonot@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$


DROP VIEW cadastro.v_endereco; --Apaga a view que tem ligação com complemento

ALTER TABLE cadastro.endereco_pessoa ALTER COLUMN complemento TYPE character varying(50); --Altera tamanho coluna complemnto

-- Cria novamento a view

CREATE OR REPLACE VIEW cadastro.v_endereco AS 
 SELECT e.idpes, e.cep, e.idlog, e.numero, e.letra, e.complemento, e.idbai, e.bloco, e.andar, e.apartamento, l.nome AS logradouro, l.idtlog, b.nome AS bairro, m.nome AS cidade, m.sigla_uf, b.zona_localizacao
   FROM endereco_pessoa e, logradouro l, bairro b, municipio m
  WHERE e.idlog = l.idlog AND e.idbai = b.idbai AND b.idmun = m.idmun AND e.tipo = 1::numeric
UNION 
 SELECT e.idpes, e.cep, NULL::"unknown" AS idlog, e.numero, e.letra, e.complemento, NULL::"unknown" AS idbai, e.bloco, e.andar, e.apartamento, e.logradouro, e.idtlog, e.bairro, e.cidade, e.sigla_uf, e.zona_localizacao
   FROM endereco_externo e
  WHERE e.tipo = 1::numeric;

ALTER TABLE cadastro.v_endereco
  OWNER TO ieducar;