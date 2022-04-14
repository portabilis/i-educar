CREATE OR REPLACE FUNCTION relatorio.prioridade_historico(situacao numeric) RETURNS numeric
    LANGUAGE plpgsql
AS $$
DECLARE
    prioridade NUMERIC := 0;
BEGIN
    prioridade := (CASE
                       WHEN situacao = 1  THEN 1
                       WHEN situacao = 12 THEN 1
                       WHEN situacao = 13 THEN 1
                       WHEN situacao = 2  THEN 2
                       WHEN situacao = 14 THEN 2
                       WHEN situacao = 3  THEN 3
                       WHEN situacao = 4  THEN 4
                       WHEN situacao = 6  THEN 4
                       ELSE 5 END);
    RETURN prioridade;
END;
$$;
