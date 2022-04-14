CREATE OR REPLACE VIEW public.persons AS
SELECT
    idpes AS id,
    nome AS name,
    idpes_cad AS created_by,
    data_cad::timestamp(0) AS created_at,
    url,
    (CASE tipo
        WHEN 'F' THEN 1 -- física
        WHEN 'J' THEN 2 -- juridica
    END) AS type,
    idpes_rev AS updated_by,
    data_rev AS updated_at,
    email,
    -- situacao,
    (CASE origem_gravacao
        WHEN 'M' THEN 1 -- migração
        WHEN 'C' THEN 2 -- cadastro
        WHEN 'U' THEN 3 -- unificação
        WHEN 'O' THEN 4 -- outro
    END) AS registry_origin
    -- operacao
FROM cadastro.pessoa;
