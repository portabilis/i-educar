CREATE OR REPLACE VIEW countries AS
SELECT
    idpais AS id,
    nome AS name,
    -- geom,
    cod_ibge AS ibge_code
FROM public.pais;
