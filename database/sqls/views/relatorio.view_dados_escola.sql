CREATE OR REPLACE VIEW relatorio.view_dados_escola AS
SELECT
    escola.cod_escola,
    relatorio.get_nome_escola(escola.cod_escola) AS nome,
    pessoa.email,
    a.postal_code AS cep,
    a."number" AS numero,
    a.id AS tipo_logradouro,
    '' AS descricao_logradouro,
    a.address AS logradouro,
    a.neighborhood AS bairro,
    a.city AS municipio,
    a.state_abbreviation AS uf_municipio,
    educacenso_cod_escola.cod_escola_inep AS inep,
    telefone.ddd AS telefone_ddd,
    to_char(telefone.fone, '99999-9999'::text) AS telefone,
    fone_pessoa.ddd AS celular_ddd,
    to_char(fone_pessoa.fone, '99999-9999'::text) AS celular
FROM pmieducar.escola
LEFT JOIN cadastro.pessoa ON escola.ref_idpes::numeric = pessoa.idpes
LEFT JOIN modules.educacenso_cod_escola ON educacenso_cod_escola.cod_escola = escola.cod_escola
LEFT JOIN person_has_place php ON php.person_id = pessoa.idpes AND php.type = 1
LEFT JOIN addresses a ON a.id = php.place_id
LEFT JOIN cadastro.fone_pessoa as telefone ON pessoa.idpes = telefone.idpes AND telefone.tipo = 1
LEFT JOIN cadastro.fone_pessoa ON pessoa.idpes = fone_pessoa.idpes AND fone_pessoa.tipo = 3
LEFT JOIN cadastro.juridica ON juridica.idpes = fone_pessoa.idpes AND juridica.idpes = escola.ref_idpes::numeric;
