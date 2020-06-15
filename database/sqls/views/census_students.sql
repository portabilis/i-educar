CREATE OR REPLACE VIEW public.census_students AS
SELECT
    cod_aluno AS id,
    cod_aluno AS student_id,
    cod_aluno_inep AS inep_code,
    nome_inep AS inep_name,
    fonte AS source,
    created_at::timestamp(0) AS created_at,
    updated_at::timestamp(0) AS updated_at
FROM modules.educacenso_cod_aluno;
