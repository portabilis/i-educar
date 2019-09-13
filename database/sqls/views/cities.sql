CREATE OR REPLACE VIEW cities AS
SELECT
    idmun AS id,
    nome AS name,
    sigla_uf AS state_id,
    -- area_km2,
    -- idmreg,
    -- idasmun,
    cod_ibge AS ibge_code,
    -- geom,
    -- tipo,
    idmun_pai AS parent_id,
    idpes_rev AS updated_by,
    idpes_cad AS created_by,
    data_rev::timestamp(0) AS updated_at,
    data_cad::timestamp(0) AS created_at,
    (CASE origem_gravacao
        WHEN 'M' THEN 1 -- migração
        WHEN 'C' THEN 2 -- cadastro
        WHEN 'U' THEN 3 -- unificação
        WHEN 'O' THEN 4 -- outro
    END) AS registry_origin
    -- operacao
FROM public.municipio;
