CREATE OR REPLACE FUNCTION public.count_weekdays(date, date) RETURNS integer
    LANGUAGE plpgsql STABLE
    AS $_$
     DECLARE
      start_date alias for $1;
      end_date alias for $2;
      tmp_date date;
      tmp_dow integer;
      -- double precision returned from extract
      tot_dow integer;
     BEGIN
       tmp_date := start_date;
       tot_dow := 0;
       WHILE (tmp_date <= end_date) LOOP
         select into tmp_dow  cast(extract(dow from tmp_date) as integer);
         IF ((tmp_dow >= 2) and (tmp_dow <= 6)) THEN
           tot_dow := (tot_dow + 1);
         END IF;
         select into tmp_date (tmp_date + interval '1 day ');
       END LOOP;
       return tot_dow;

     END;
  $_$;
