CREATE VIEW public.educacenso_record40 AS
SELECT
    40 AS registro,
    educacenso_cod_escola.cod_escola_inep AS "inepEscola",
    juridica.fantasia AS "nomeEscola",
    school_managers.employee_id AS "codigoPessoa",
    pessoa.nome AS "nomePessoa",
    educacenso_cod_docente.cod_docente_inep AS "inepGestor",
    school_managers.role_id AS cargo,
    school_managers.access_criteria_id AS "criterioAcesso",
    school_managers.link_type_id AS "tipoVinculo",
    escola.dependencia_administrativa AS "dependenciaAdministrativa",
    escola.situacao_funcionamento AS "situacaoFuncionamento",
    escola.cod_escola AS "codEscola"
FROM school_managers
JOIN pmieducar.escola ON escola.cod_escola = school_managers.school_id
JOIN cadastro.juridica ON juridica.idpes = escola.ref_idpes
LEFT JOIN modules.educacenso_cod_escola ON educacenso_cod_escola.cod_escola = escola.cod_escola
LEFT JOIN pmieducar.servidor ON servidor.cod_servidor = school_managers.employee_id
LEFT JOIN cadastro.pessoa ON pessoa.idpes = school_managers.employee_id
LEFT JOIN modules.educacenso_cod_docente ON educacenso_cod_docente.cod_servidor = servidor.cod_servidor
