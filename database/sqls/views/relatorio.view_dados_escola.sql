CREATE OR REPLACE VIEW relatorio.view_dados_escola AS
SELECT escola.cod_escola,
    relatorio.get_nome_escola(escola.cod_escola) AS nome,
    pessoa.email,
    COALESCE(endereco_pessoa.cep, endereco_externo.cep) AS cep,
    COALESCE(endereco_pessoa.numero, endereco_externo.numero) AS numero,
    COALESCE(logradouro.idtlog, endereco_externo.idtlog) AS tipo_logradouro,
    tipo_logradouro.descricao AS descricao_logradouro,
    COALESCE(logradouro.nome, endereco_externo.logradouro) AS logradouro,
    COALESCE(bairro.nome, endereco_externo.bairro) AS bairro,
    COALESCE(municipio.nome, endereco_externo.cidade) AS municipio,
    COALESCE(municipio.sigla_uf, endereco_externo.sigla_uf::character varying) AS uf_municipio,
    educacenso_cod_escola.cod_escola_inep AS inep,
    telefone.ddd AS telefone_ddd,
    to_char(telefone.fone, '99999-9999'::text) AS telefone,
    fone_pessoa.ddd AS celular_ddd,
    to_char(fone_pessoa.fone, '99999-9999'::text) AS celular
FROM pmieducar.escola
LEFT JOIN cadastro.pessoa ON escola.ref_idpes::numeric = pessoa.idpes
LEFT JOIN modules.educacenso_cod_escola ON educacenso_cod_escola.cod_escola = escola.cod_escola
LEFT JOIN cadastro.endereco_pessoa ON endereco_pessoa.idpes = pessoa.idpes
LEFT JOIN cadastro.endereco_externo ON endereco_externo.idpes = pessoa.idpes
LEFT JOIN public.logradouro ON logradouro.idlog = endereco_pessoa.idlog
LEFT JOIN urbano.tipo_logradouro ON tipo_logradouro.idtlog::text = COALESCE(logradouro.idtlog, endereco_externo.idtlog)::text
LEFT JOIN public.bairro ON bairro.idbai = endereco_pessoa.idbai
LEFT JOIN public.municipio ON municipio.idmun = bairro.idmun
LEFT JOIN cadastro.fone_pessoa as telefone ON pessoa.idpes = telefone.idpes AND telefone.tipo = 1
LEFT JOIN cadastro.fone_pessoa ON pessoa.idpes = fone_pessoa.idpes AND fone_pessoa.tipo = 3
LEFT JOIN cadastro.juridica ON juridica.idpes = fone_pessoa.idpes AND juridica.idpes = escola.ref_idpes::numeric;
