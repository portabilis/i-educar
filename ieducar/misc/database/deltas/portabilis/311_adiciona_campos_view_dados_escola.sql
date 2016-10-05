-- @author   Maurício Citadini Biléssimo <mauricio@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$
-- Adiciona as colunas celular_ddd, celular, tipo_logradouro

DROP VIEW relatorio.view_dados_escola;

CREATE OR REPLACE VIEW relatorio.view_dados_escola AS 
 	SELECT escola.cod_escola, 
 		   relatorio.get_nome_escola(escola.cod_escola) AS nome, 
 		   pessoa.email, 
 		   COALESCE(endereco_pessoa.cep, endereco_externo.cep) AS cep, 
 		   COALESCE(endereco_pessoa.numero, endereco_externo.numero) AS numero, 
 		   COALESCE(logradouro.idtlog, endereco_externo.idtlog) AS tipo_logradouro, 
 		   COALESCE(logradouro.nome, endereco_externo.logradouro) AS logradouro, 
 		   COALESCE(bairro.nome, endereco_externo.bairro) AS bairro, 
 		   COALESCE(municipio.nome, endereco_externo.cidade) AS municipio, 
 		   COALESCE(municipio.sigla_uf, endereco_externo.sigla_uf::character varying) AS uf_municipio, 
 		   educacenso_cod_escola.cod_escola_inep AS inep, 
 		   relatorio.get_ddd_escola(escola.cod_escola) AS telefone_ddd, 
 		   relatorio.get_telefone_escola(escola.cod_escola) AS telefone, 
 		   fone_pessoa.ddd AS celular_ddd, 
 		   to_char(fone_pessoa.fone, '99999-9999'::text) AS celular
   	  FROM escola
   	  JOIN pessoa ON escola.ref_idpes::numeric = pessoa.idpes
 LEFT JOIN educacenso_cod_escola ON educacenso_cod_escola.cod_escola = escola.cod_escola
 LEFT JOIN endereco_pessoa ON endereco_pessoa.idpes = pessoa.idpes
 LEFT JOIN endereco_externo ON endereco_externo.idpes = pessoa.idpes
 LEFT JOIN logradouro ON logradouro.idlog = endereco_pessoa.idlog
 LEFT JOIN bairro ON bairro.idbai = endereco_pessoa.idbai
 LEFT JOIN municipio ON municipio.idmun = bairro.idmun
 LEFT JOIN fone_pessoa ON pessoa.idpes = fone_pessoa.idpes AND fone_pessoa.tipo = 3::numeric
 LEFT JOIN juridica ON juridica.idpes = fone_pessoa.idpes AND juridica.idpes = escola.ref_idpes::numeric;

ALTER TABLE relatorio.view_dados_escola
  OWNER TO ieducar;