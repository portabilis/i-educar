CREATE OR REPLACE FUNCTION pmieducar.retorna_database_transferencia(ano INTEGER, instituicao_id INTEGER) RETURNS DATE
    LANGUAGE plpgsql
as
$$
DECLARE
    data_transferencia DATE;
BEGIN
    SELECT data_base_transferencia
    FROM pmieducar.instituicao
    WHERE cod_instituicao = instituicao_id
    INTO data_transferencia;

    IF TO_CHAR(data_transferencia, 'mm-dd') <> '02-29' THEN
        RETURN ano || TO_CHAR(data_transferencia - INTERVAL '1 DAY', '-mm-dd');
    END IF;

    IF (SELECT (ano % 4 = 0) AND ((ano % 100 <> 0) or (ano % 400 = 0))) THEN
        RETURN ano || TO_CHAR(data_transferencia, '-mm-dd');
    ELSE
        RETURN ano || TO_CHAR(data_transferencia - INTERVAL '1 DAY', '-mm-dd');
    END IF;
END;
$$;
