CREATE OR REPLACE VIEW public.religions AS
SELECT
    cod_religiao AS id,
    ref_usuario_exc AS deleted_by,
    ref_usuario_cad AS created_by,
    nm_religiao AS name,
    data_cadastro::timestamp(0) AS created_at,
    (CASE
        WHEN ativo = 0 THEN data_exclusao
        ELSE NULL
    END)::timestamp(0) AS deleted_at
FROM pmieducar.religiao;
