CREATE OR REPLACE FUNCTION relatorio.get_situacao_historico_abreviado(integer) RETURNS character varying
    LANGUAGE sql
AS $_$
SELECT CASE
           WHEN $1 = 1 THEN 'Apr'::character varying
           WHEN $1 = 2 THEN 'Rep'::character varying
           WHEN $1 = 3 THEN 'Cur'::character varying
           WHEN $1 = 4 THEN 'Trs'::character varying
           WHEN $1 = 5 THEN 'Recl'::character varying
           WHEN $1 = 6 THEN 'Aba'::character varying
           WHEN $1 = 12 THEN 'ApDp'::character varying
           WHEN $1 = 13 THEN 'ApCo'::character varying
           WHEN $1 = 14 THEN 'RpFt'::character varying
           ELSE ''::character varying
           END AS situacao; $_$;
