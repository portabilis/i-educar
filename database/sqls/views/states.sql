CREATE OR REPLACE VIEW states AS
SELECT
    sigla_uf AS id,
    sigla_uf AS abbreviation,
    nome AS name,
    -- geom,
    idpais AS country_id,
    cod_ibge AS ibge_code
FROM public.uf;
