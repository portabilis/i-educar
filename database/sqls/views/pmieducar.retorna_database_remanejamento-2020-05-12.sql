CREATE OR REPLACE FUNCTION pmieducar.retorna_database_remanejamento(ano INTEGER, instituicao_id INTEGER) RETURNS DATE
    LANGUAGE plpgsql
as
$$
DECLARE
    data_remanejamento DATE;
BEGIN
    SELECT data_base_remanejamento
    FROM pmieducar.instituicao
    WHERE cod_instituicao = instituicao_id
    INTO data_remanejamento;

    IF TO_CHAR(data_remanejamento, 'mm-dd') <> '02-29' THEN
        RETURN ano || TO_CHAR(data_remanejamento - INTERVAL '1 DAY', '-mm-dd');
    END IF;

    IF (SELECT (ano % 4 = 0) AND ((ano % 100 <> 0) or (ano % 400 = 0))) THEN
        RETURN ano || TO_CHAR(data_remanejamento, '-mm-dd');
    ELSE
        RETURN ano || TO_CHAR(data_remanejamento - INTERVAL '1 DAY', '-mm-dd');
    END IF;
END ;
$$;
