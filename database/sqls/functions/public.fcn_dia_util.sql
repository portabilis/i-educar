CREATE FUNCTION public.fcn_dia_util(date, date) RETURNS integer
    LANGUAGE plpgsql
    AS $_$
DECLARE
  -- Parâmetro recebidos
  v_dt_ini ALIAS for $1;
  v_dt_fim ALIAS for $2;
  v_dt_ini_x date;
  v_qtde integer;
BEGIN
  v_qtde := 0;
  v_dt_ini_x := v_dt_ini;
  WHILE v_dt_ini_x <= v_dt_fim LOOP
    IF to_char(v_dt_ini_x,'D') NOT IN ('1','7') THEN
      IF NOT EXISTS(SELECT idfer
             from servicos.feriado
             WHERE to_char(data,'DD/MM/YYYY') =
             to_char(v_dt_ini_x,'DD/MM/YYYY')) THEN
        v_qtde := v_qtde + 1;
      END IF;
    END IF;
    v_dt_ini_x := v_dt_ini_x + interval '1 day';
  END LOOP;
  RETURN v_qtde;
END;$_$;
