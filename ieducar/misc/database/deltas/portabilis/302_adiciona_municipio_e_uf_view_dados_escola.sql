-- Adiciona municipio e sigla do municipio na view de dados da escola
-- @author   Paula Bonot <bonot@portabilis.com.br>

DROP VIEW relatorio.view_dados_escola;

CREATE OR REPLACE VIEW relatorio.view_dados_escola AS
 SELECT escola.cod_escola,
        relatorio.get_nome_escola(escola.cod_escola) AS nome,
        pessoa.email,
        COALESCE(endereco_pessoa.cep, endereco_externo.cep) AS cep,
        COALESCE(endereco_pessoa.numero, endereco_externo.numero) AS numero,
        COALESCE(logradouro.nome, endereco_externo.logradouro) AS logradouro,
        COALESCE(bairro.nome, endereco_externo.bairro) AS bairro,
        COALESCE(municipio.nome, endereco_externo.cidade) AS municipio,
        COALESCE(municipio.sigla_uf, endereco_externo.sigla_uf) AS uf_municipio,
        educacenso_cod_escola.cod_escola_inep AS inep,
        relatorio.get_telefone_escola(escola.cod_escola) AS telefone,
        relatorio.get_ddd_escola(escola.cod_escola) AS telefone_ddd
   FROM escola
   JOIN pessoa ON escola.ref_idpes::numeric = pessoa.idpes
   LEFT JOIN educacenso_cod_escola ON educacenso_cod_escola.cod_escola = escola.cod_escola
   LEFT JOIN endereco_pessoa ON endereco_pessoa.idpes = pessoa.idpes
   LEFT JOIN endereco_externo ON endereco_externo.idpes = pessoa.idpes
   LEFT JOIN logradouro ON logradouro.idlog = endereco_pessoa.idlog
   LEFT JOIN bairro ON bairro.idbai = endereco_pessoa.idbai
   LEFT JOIN municipio ON municipio.idmun = bairro.idmun;

ALTER TABLE relatorio.view_dados_escola
  OWNER TO ieducar;