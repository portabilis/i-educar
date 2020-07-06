CREATE OR REPLACE FUNCTION pmieducar.get_date_in_year(year INTEGER, date DATE) RETURNS DATE
    LANGUAGE plpgsql
as
$$
BEGIN
    IF TO_CHAR(date, 'mm-dd') <> '02-29' THEN
        RETURN year || TO_CHAR(date, '-mm-dd');
    END IF;

    IF (SELECT (year % 4 = 0) AND ((year % 100 <> 0) or (year % 400 = 0))) THEN
        RETURN year || TO_CHAR(date, '-mm-dd');
    ELSE
        RETURN year || TO_CHAR(date - INTERVAL '1 DAY', '-mm-dd');
    END IF;
END;
$$;
