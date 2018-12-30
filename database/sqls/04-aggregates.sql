CREATE AGGREGATE public.textcat_all(text) (
    SFUNC = public.commacat_ignore_nulls,
    STYPE = text,
    INITCOND = ''
);
