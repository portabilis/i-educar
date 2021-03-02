CREATE OR REPLACE FUNCTION relatorio.get_nacionalidade(nacionalidade_id NUMERIC)
    RETURNS CHARACTER VARYING
    LANGUAGE plpgsql
AS $$
BEGIN RETURN
    (SELECT CASE
                WHEN nacionalidade_id = 1
                    THEN 'Brasileira'
                WHEN nacionalidade_id = 2
                    THEN 'Naturalizado Brasileiro'
                WHEN nacionalidade_id = 3
                    THEN 'Estrangeira'
                END);
END;
$$;
