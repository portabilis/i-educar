CREATE OR REPLACE VIEW religions AS
SELECT
    cod_religiao AS id,
    ref_usuario_exc AS who_deleted,
    ref_usuario_cad AS who_created,
    nm_religiao AS name,
    data_cadastro::timestamp(0) AS created_at,
    (CASE
        WHEN ativo = 0 THEN data_exclusao
        ELSE NULL
    END)::timestamp(0) AS deleted_at
FROM pmieducar.religiao;
