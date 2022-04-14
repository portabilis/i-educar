CREATE OR REPLACE FUNCTION relatorio.count_weekdays(start_date date, end_date date) RETURNS integer
    LANGUAGE plpgsql
    AS $$
                        DECLARE
                          tmp_date date;
                          tmp_dow integer;
                          -- double precision returned from extract
                          tot_dow integer;
                        BEGIN
                          tmp_date := start_date;
                          tot_dow := 0;

                          WHILE (tmp_date <= end_date) LOOP
                            SELECT INTO tmp_dow cast(extract(dow
                              FROM tmp_date) AS integer);

                            IF ((tmp_dow >= 2) AND (tmp_dow <= 6)) THEN
                              tot_dow := (tot_dow + 1);
                            END IF;

                            SELECT INTO tmp_date (tmp_date + interval '1 DAY ');

                          END LOOP;

                          RETURN tot_dow;
                        END; $$;
